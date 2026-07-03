<?php

namespace App\Http\Controllers\Kph;

use App\Http\Controllers\Controller;
use App\Models\Bkph;
use App\Models\Petak;
use App\Models\Realisasi;
use App\Models\Target;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Ringkasan & progres per petak — gabungan kartu metrik, accordion filter
     * progres per petak, target periode berjalan, dan grafik realisasi vs target.
     */
    public function ringkasan(Request $request)
    {
        $bulan = (int) now()->month;
        $tahun = (int) now()->year;

        $bkphList = Bkph::with([
            'rph.petak.realisasi' => fn ($q) => $q->valid()->orderByDesc('tanggal_update')->orderByDesc('versi_input'),
        ])->orderBy('nama_bkph')->get();

        // ambil realisasi TERBARU per petak (versi valid terakhir)
        $totalPetak = 0;
        $petakSelesai = 0;
        $petakRendah = 0;
        $bkphRows = [];

        foreach ($bkphList as $bkph) {
            $totalRealPohonBkph = 0;
            $totalTargetPohonBkph = 0;

            foreach ($bkph->rph as $rph) {
                foreach ($rph->petak as $petak) {
                    $totalPetak++;
                    $latest = $petak->realisasi->first();
                    $persen = $latest ? (float) $latest->persentase_capaian : 0;

                    if ($latest) {
                        $totalRealPohonBkph += $latest->jumlah_pohon_realisasi;
                    }
                    $totalTargetPohonBkph += $petak->total_pohon ?? 0;

                    if ($persen >= 80) {
                        $petakSelesai++;
                    } elseif ($persen < 40 && $latest) {
                        $petakRendah++;
                    }
                }
            }

            $targetBkphBulanIni = Target::where('level_target', 'BKPH')
                ->where('id_bkph', $bkph->id_bkph)
                ->where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->value('target_persen');

            $bkphRows[] = [
                'bkph' => $bkph,
                'target_persen' => $targetBkphBulanIni,
                'realisasi_persen' => $totalTargetPohonBkph > 0
                    ? round($totalRealPohonBkph / $totalTargetPohonBkph * 100, 1)
                    : 0,
                'realisasi_pohon' => $totalRealPohonBkph,
                'target_pohon' => $totalTargetPohonBkph,
            ];
        }

        $totalRealisasiPohonBulanIni = Realisasi::valid()
            ->whereMonth('tanggal_update', $bulan)
            ->whereYear('tanggal_update', $tahun)
            ->sum('jumlah_pohon_realisasi');

        $totalTargetPohon = collect($bkphRows)->sum('target_pohon');

        // grafik realisasi vs target Jan..Des
        $chart = [];
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($m = 1; $m <= 12; $m++) {
            $targetRerata = Target::where('level_target', 'BKPH')
                ->where('periode_bulan', $m)->where('periode_tahun', $tahun)
                ->avg('target_persen');

            $real = null;
            if ($m <= $bulan) {
                $realPohon = Realisasi::valid()
                    ->whereMonth('tanggal_update', $m)->whereYear('tanggal_update', $tahun)
                    ->sum('jumlah_pohon_realisasi');
                $real = $totalTargetPohon > 0 ? round($realPohon / $totalTargetPohon * 100) : 0;
            }

            $chart[] = [
                'label' => $namaBulan[$m - 1],
                'target' => $targetRerata ? round($targetRerata) : 0,
                'real' => $real,
            ];
        }

        return view('kph.ringkasan', [
            'totalPetak' => $totalPetak,
            'bkphCount' => $bkphList->count(),
            'rphCount' => $bkphList->sum(fn ($b) => $b->rph->count()),
            'petakSelesai' => $petakSelesai,
            'petakRendah' => $petakRendah,
            'realisasiBulanIni' => $totalRealisasiPohonBulanIni,
            'targetPohonBulanIni' => $totalTargetPohon,
            'bkphRows' => $bkphRows,
            'chart' => $chart,
            'filterBkph' => Bkph::orderBy('nama_bkph')->get(),
        ]);
    }

    /**
     * Peta sebaran — koordinat GPS tiap BKPH beserta luas & realisasi rerata.
     */
    public function peta()
    {
        $bulan = (int) now()->month;
        $tahun = (int) now()->year;

        $bkphList = Bkph::with('rph.petak.realisasi')->orderBy('nama_bkph')->get()->map(function ($bkph) {
            $totalReal = 0;
            $totalTarget = 0;
            foreach ($bkph->rph as $rph) {
                foreach ($rph->petak as $petak) {
                    $latest = $petak->realisasi->where('status_validasi', 'Valid')
                        ->sortByDesc('tanggal_update')->first();
                    if ($latest) {
                        $totalReal += $latest->jumlah_pohon_realisasi;
                    }
                    $totalTarget += $petak->total_pohon ?? 0;
                }
            }
            $bkph->realisasi_persen = $totalTarget > 0 ? round($totalReal / $totalTarget * 100, 1) : 0;

            return $bkph;
        });

        return view('kph.peta', compact('bkphList'));
    }
}
