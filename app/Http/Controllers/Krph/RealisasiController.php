<?php

namespace App\Http\Controllers\Krph;

use App\Http\Controllers\Controller;
use App\Models\DokumentasiFoto;
use App\Models\LogAktivitas;
use App\Models\Mandor;
use App\Models\Petak;
use App\Models\Realisasi;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RealisasiController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        $rph = $user->rph()->with('petak.realisasi', 'mandor', 'bkph')->firstOrFail();

        $petakSelectedId = (int) $request->query('petak', $rph->petak->first()?->id_petak);
        $petakSelected = $rph->petak->firstWhere('id_petak', $petakSelectedId);
        $latestPetakStatus = $petakSelected?->realisasi->sortByDesc('tanggal_update')->first();

        $target = Target::where('level_target', 'RPH')->where('id_rph', $rph->id_rph)
            ->where('periode_bulan', now()->month)->where('periode_tahun', now()->year)->first();

        $statusTerakhir = Realisasi::with('petak')
            ->whereIn('id_petak', $rph->petak->pluck('id_petak'))
            ->where('id_user', $user->id_user)
            ->orderByDesc('tanggal_update')
            ->limit(5)
            ->get();

        $petakList = $rph->petak()->orderBy('kode_petak')->get();

        return view('krph.input', [
            'rph' => $rph,
            'mandorList' => $rph->mandor()->where('status_aktif', true)->orderBy('nama_mandor')->get(),
            'petakList' => $petakList,
            'petakSelected' => $petakSelected,
            'latestPetakStatus' => $latestPetakStatus,
            'targetPersen' => $target?->target_persen ?? 70,
            'statusTerakhir' => $statusTerakhir,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_petak' => ['required', 'exists:petak,id_petak'],
            'id_mandor' => ['nullable', 'exists:mandor,id_mandor'],
            'tanggal_update' => ['required', 'date'],
            'jumlah_pohon_realisasi' => ['required', 'integer', 'min:0'],
            'catatan_lapangan' => ['nullable', 'string'],
            'foto' => ['required', 'image', 'max:5120'],
        ], [
            'foto.required' => 'Dokumentasi foto wajib diunggah — data realisasi tanpa foto tidak dapat disimpan.',
        ]);

        $user = $request->user();
        $petak = Petak::findOrFail($data['id_petak']);

        // pastikan petak ini memang milik RPH user yang sedang login
        abort_unless($petak->id_rph === $user->id_rph, 403);

        $versiSebelumnya = Realisasi::where('id_petak', $petak->id_petak)->orderByDesc('versi_input')->first();

        $path = $request->file('foto')->store('dokumentasi-foto', 'public');

        $realisasi = Realisasi::create([
            'id_petak' => $petak->id_petak,
            'id_mandor' => $data['id_mandor'] ?? null,
            'id_user' => $user->id_user,
            'jumlah_pohon_realisasi' => $data['jumlah_pohon_realisasi'],
            'total_pohon_petak' => $petak->total_pohon ?? $data['jumlah_pohon_realisasi'],
            'tanggal_update' => $data['tanggal_update'],
            'catatan_lapangan' => $data['catatan_lapangan'] ?? null,
            'status_validasi' => 'Valid',
            'versi_input' => $versiSebelumnya ? $versiSebelumnya->versi_input + 1 : 1,
            'id_realisasi_sebelumnya' => $versiSebelumnya?->id_realisasi,
        ]);

        DokumentasiFoto::create([
            'id_realisasi' => $realisasi->id_realisasi,
            'path_foto' => $path,
            'keterangan' => 'Foto sampel pohon terpasang talang',
            'tanggal_upload' => now(),
        ]);

        LogAktivitas::catat($user->id_user, "Input realisasi petak {$petak->kode_petak}");

        return redirect()->route('krph.input', ['petak' => $petak->id_petak])
            ->with('status', "Data realisasi petak {$petak->kode_petak} berhasil disimpan.");
    }
}
