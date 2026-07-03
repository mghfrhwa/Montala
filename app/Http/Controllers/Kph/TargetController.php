<?php

namespace App\Http\Controllers\Kph;

use App\Http\Controllers\Controller;
use App\Models\Bkph;
use App\Models\Realisasi;
use App\Models\Rph;
use App\Models\Target;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);
        $bulanBerjalan = (int) now()->month;

        $bkphList = Bkph::orderBy('nama_bkph')->get();

        // tabel target tahunan per BKPH (12 kolom bulan)
        $rows = $bkphList->map(function ($bkph) use ($tahun, $bulanBerjalan) {
            // id petak & total kapasitas pohon BKPH ini, dihitung sekali saja (bukan per bulan)
            $petakIds = $bkph->rph()->with('petak')->get()->flatMap->petak->pluck('id_petak');
            $totalKapasitasPohon = $bkph->rph()->with('petak')->get()->flatMap->petak->sum('total_pohon');

            $bulanData = [];
            for ($m = 1; $m <= 12; $m++) {
                $target = Target::where('level_target', 'BKPH')
                    ->where('id_bkph', $bkph->id_bkph)
                    ->where('periode_bulan', $m)->where('periode_tahun', $tahun)
                    ->first();

                $status = $m < $bulanBerjalan ? 'lalu' : ($m === $bulanBerjalan ? 'berjalan' : 'depan');

                $targetPohon = $target?->jumlah_target_pohon;
                $realisasiPohon = null;
                $realisasiPersen = null;

                if ($status !== 'depan') {
                    $realisasiPohon = Realisasi::valid()
                        ->whereIn('id_petak', $petakIds)
                        ->whereMonth('tanggal_update', $m)->whereYear('tanggal_update', $tahun)
                        ->sum('jumlah_pohon_realisasi');

                    // persentase capaian = realisasi pohon dibagi TARGET pohon bulan itu (bukan total kapasitas)
                    $realisasiPersen = ($targetPohon && $targetPohon > 0)
                        ? round($realisasiPohon / $targetPohon * 100)
                        : 0;
                }

                $bulanData[$m] = [
                    'status' => $status,
                    'target_pohon' => $targetPohon,
                    'target_persen' => $target?->target_persen,
                    'realisasi_pohon' => $realisasiPohon,
                    'realisasi_persen' => $realisasiPersen,
                ];
            }

            return ['bkph' => $bkph, 'bulan' => $bulanData, 'total_kapasitas_pohon' => $totalKapasitasPohon];
        });

        // rincian target per RPH untuk BKPH terpilih
        $bkphPickId = (int) $request->query('bkph', $bkphList->first()?->id_bkph);
        $rphList = Rph::where('id_bkph', $bkphPickId)->with('petak')->orderBy('nama_rph')->get()->map(function ($rph) use ($bulanBerjalan, $tahun) {
            $target = Target::where('level_target', 'RPH')->where('id_rph', $rph->id_rph)
                ->where('periode_bulan', $bulanBerjalan)->where('periode_tahun', $tahun)->first();

            $targetPohon = $target?->jumlah_target_pohon;
            $totalRealPohon = Realisasi::valid()->whereIn('id_petak', $rph->petak->pluck('id_petak'))
                ->whereMonth('tanggal_update', $bulanBerjalan)->whereYear('tanggal_update', $tahun)
                ->sum('jumlah_pohon_realisasi');

            return [
                'rph' => $rph,
                'target_pohon' => $targetPohon,
                'target_persen' => $target?->target_persen,
                'realisasi_pohon' => $totalRealPohon,
                'realisasi_persen' => ($targetPohon && $targetPohon > 0) ? round($totalRealPohon / $targetPohon * 100) : 0,
            ];
        });

        return view('kph.target', [
            'tahun' => $tahun,
            'bulanBerjalan' => $bulanBerjalan,
            'rows' => $rows,
            'bkphList' => $bkphList,
            'bkphPickId' => $bkphPickId,
            'rphList' => $rphList,
        ]);
    }

    /**
     * Simpan perubahan target (bulan mendatang, level BKPH) — upsert per BKPH+bulan.
     * Payload: target[<id_bkph>][<bulan>] = jumlah pohon
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun' => ['required', 'integer'],
            'target' => ['required', 'array'],
            'target.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        // cache total kapasitas pohon per BKPH supaya tidak query berulang
        $kapasitasCache = [];

        foreach ($data['target'] as $idBkph => $bulanPohon) {
            if (! isset($kapasitasCache[$idBkph])) {
                $bkph = Bkph::find($idBkph);
                $kapasitasCache[$idBkph] = $bkph
                    ? $bkph->rph()->with('petak')->get()->flatMap->petak->sum('total_pohon')
                    : 0;
            }
            $totalKapasitas = $kapasitasCache[$idBkph];

            foreach ($bulanPohon as $bulan => $jumlahPohon) {
                if ($jumlahPohon === null || $jumlahPohon === '') {
                    continue;
                }

                $jumlahPohon = (int) $jumlahPohon;
                $targetPersen = $totalKapasitas > 0 ? round($jumlahPohon / $totalKapasitas * 100, 2) : 0;

                Target::updateOrCreate(
                    ['level_target' => 'BKPH', 'id_bkph' => $idBkph, 'periode_bulan' => $bulan, 'periode_tahun' => $data['tahun']],
                    [
                        'id_user' => $request->user()->id_user,
                        'jumlah_target_pohon' => $jumlahPohon,
                        'target_persen' => $targetPersen,
                        'status_periode' => 'Mendatang',
                    ]
                );
            }
        }

        return redirect()->route('kph.target', ['tahun' => $data['tahun']])->with('status', 'Target berhasil disimpan.');
    }
}