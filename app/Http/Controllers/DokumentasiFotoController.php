<?php

namespace App\Http\Controllers;

use App\Models\DokumentasiFoto;
use Illuminate\Http\Request;

class DokumentasiFotoController extends Controller
{
    /**
     * Tampilkan detail satu foto dokumentasi (dipakai dari progres per petak,
     * riwayat input, maupun aktivitas input terbaru di halaman export KPH).
     * KPH boleh lihat semua foto; KRPH hanya foto di wilayah RPH-nya sendiri.
     */
    public function show(Request $request, DokumentasiFoto $dokumentasiFoto)
    {
        $user = $request->user();
        $realisasi = $dokumentasiFoto->realisasi()->with('petak.rph.bkph', 'user')->firstOrFail();

        if ($user->isKrph() && $realisasi->petak->id_rph !== $user->id_rph) {
            abort(403);
        }

        return view('foto.show', compact('dokumentasiFoto', 'realisasi'));
    }
}
