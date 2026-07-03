<?php

namespace App\Http\Controllers\Kph;

use App\Http\Controllers\Controller;
use App\Models\Bkph;
use App\Models\Realisasi;
use App\Models\Rph;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index()
    {
        $aktivitasTerbaru = Realisasi::with(['petak.rph.bkph', 'user', 'dokumentasiFoto'])
            ->orderByDesc('tanggal_update')
            ->orderByDesc('id_realisasi')
            ->limit(10)
            ->get();

        return view('kph.export', [
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'bkphList' => Bkph::orderBy('nama_bkph')->get(),
            'rphList' => Rph::orderBy('nama_rph')->get(),
        ]);
    }

    /**
     * Unduh laporan sebagai CSV (dibuka native oleh Excel).
     * Catatan: untuk file .xlsx asli dengan styling, tambahkan package
     * "maatwebsite/excel" lalu ganti implementasi ini dengan sebuah
     * FromCollection/Export class — struktur data di bawah sudah siap dipakai.
     */
    public function unduh(Request $request): StreamedResponse
    {
        $data = $request->validate([
            'level' => ['nullable', 'string'],
            'id_bkph' => ['nullable', 'integer'],
            'id_rph' => ['nullable', 'integer'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ]);

        $query = Realisasi::with(['petak.rph.bkph', 'user'])->valid();

        if (! empty($data['id_bkph'])) {
            $query->whereHas('petak.rph', fn ($q) => $q->where('id_bkph', $data['id_bkph']));
        }
        if (! empty($data['id_rph'])) {
            $query->whereHas('petak', fn ($q) => $q->where('id_rph', $data['id_rph']));
        }
        if (! empty($data['dari'])) {
            $query->whereDate('tanggal_update', '>=', $data['dari']);
        }
        if (! empty($data['sampai'])) {
            $query->whereDate('tanggal_update', '<=', $data['sampai']);
        }
        if (! empty($data['status'])) {
            $query->where(function ($q) use ($data) {
                match ($data['status']) {
                    'selesai' => $q->where('persentase_capaian', '>=', 80),
                    'berjalan' => $q->whereBetween('persentase_capaian', [40, 79.99]),
                    'belum' => $q->where('persentase_capaian', '<', 40),
                    default => null,
                };
            });
        }

        $rows = $query->orderByDesc('tanggal_update')->get();

        $filename = 'laporan-sitalang-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'BKPH', 'RPH', 'Petak', 'Target Pohon', 'Realisasi Pohon', 'Capaian (%)', 'Status', 'Diinput Oleh']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    optional($r->tanggal_update)->format('d-m-Y'),
                    $r->petak?->rph?->bkph?->nama_bkph,
                    $r->petak?->rph?->nama_rph,
                    $r->petak?->kode_petak,
                    $r->total_pohon_petak,
                    $r->jumlah_pohon_realisasi,
                    $r->persentase_capaian,
                    $r->status_validasi,
                    $r->user?->nama,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
