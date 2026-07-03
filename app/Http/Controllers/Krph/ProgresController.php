<?php

namespace App\Http\Controllers\Krph;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgresController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $rph = $user->rph()->with('petak.realisasi')->firstOrFail();

        $bulan = (int) now()->month;
        $tahun = (int) now()->year;

        $totalTargetPohon = 0;
        $totalRealPohon = 0;
        $petakSelesai = 0;
        $perluPerbaikan = 0;

        $petakRows = $rph->petak->map(function ($petak) use (&$totalTargetPohon, &$totalRealPohon, &$petakSelesai, &$perluPerbaikan) {
            $latest = $petak->realisasi->sortByDesc('tanggal_update')->first();
            $totalTargetPohon += $petak->total_pohon ?? 0;

            if ($latest && $latest->status_validasi === 'Valid') {
                $totalRealPohon += $latest->jumlah_pohon_realisasi;
                if ($latest->persentase_capaian >= 80) {
                    $petakSelesai++;
                }
            }
            if ($latest && $latest->status_validasi === 'Tidak Valid') {
                $perluPerbaikan++;
            }

            return ['petak' => $petak, 'latest' => $latest];
        });

        $targetRph = $rph->target()->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->first();
        $realisasiPersen = $totalTargetPohon > 0 ? round($totalRealPohon / $totalTargetPohon * 100) : 0;

        return view('krph.progres', [
            'rph' => $rph,
            'petakRows' => $petakRows,
            'realisasiPersen' => $realisasiPersen,
            'targetPersen' => $targetRph?->target_persen ?? 70,
            'totalRealPohon' => $totalRealPohon,
            'totalTargetPohon' => $totalTargetPohon,
            'petakSelesai' => $petakSelesai,
            'perluPerbaikan' => $perluPerbaikan,
        ]);
    }
}
