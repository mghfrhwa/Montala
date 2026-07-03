<?php

namespace App\Http\Controllers\Krph;

use App\Http\Controllers\Controller;
use App\Models\Realisasi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $rph = $user->rph()->with('petak')->firstOrFail();

        $query = Realisasi::with(['petak.rph.bkph', 'dokumentasiFoto'])
            ->whereIn('id_petak', $rph->petak->pluck('id_petak'))
            ->where('id_user', $user->id_user);

        if ($bulanTahun = $request->query('bulan')) {
            [$namaBulan, $tahun] = array_pad(explode(' ', $bulanTahun), 2, null);
            $bulanIndex = array_search($namaBulan, ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], true);
            if ($bulanIndex && $tahun) {
                $query->whereMonth('tanggal_update', $bulanIndex)->whereYear('tanggal_update', $tahun);
            }
        }
        if ($petak = $request->query('petak')) {
            $query->whereHas('petak', fn ($q) => $q->where('kode_petak', $petak));
        }

        $riwayat = $query->orderByDesc('tanggal_update')->paginate(20)->withQueryString();

        return view('krph.riwayat', [
            'rph' => $rph,
            'riwayat' => $riwayat,
            'petakList' => $rph->petak,
        ]);
    }
}
