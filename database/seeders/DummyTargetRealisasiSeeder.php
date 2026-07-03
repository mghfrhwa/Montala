<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyTargetRealisasiSeeder extends Seeder
{
    public function run(): void
    {
        $bulanList       = [1, 2, 3, 4, 5, 6, 7]; // Januari - Juli 2026 (Juli = bulan berjalan)
        $tahun           = 2026;
        $bulanSekarang   = 7; // Juli 2026, dianggap bulan berjalan saat ini
        $tanggalSekarang = 3; // tanggal 3 Juli 2026 (current date), batas tanggal_update utk bulan berjalan

        $bkphList = DB::table('bkph')->get();
        $rphList  = DB::table('rph')->get();

        // total pohon per BKPH = jumlah pohon seluruh RPH di bawahnya
        $totalPohonPerBkph = $rphList->groupBy('id_bkph')
            ->map(fn ($group) => $group->sum('jumlah_pohon'));

        // 1. Pastikan setiap RPH punya user KRPH (buat kalau belum ada)
        foreach ($rphList as $rph) {
            $existing = DB::table('user')->where('id_rph', $rph->id_rph)->first();

            if (! $existing) {
                $usernameBase = 'asper.' . strtolower(str_replace(' ', '.', $rph->nama_rph));

                User::create([
                    'nama'         => 'Asper ' . ucwords(strtolower($rph->nama_rph)),
                    'username'     => $usernameBase,
                    'password'     => Hash::make('krph123'),
                    'role'         => 'KRPH',
                    'id_rph'       => $rph->id_rph,
                    'status_aktif' => true,
                ]);
            }
        }

        $userKph = DB::table('user')->where('role', 'KPH')->first();

        if (! $userKph) {
            $this->command->error('Tidak ada user dengan role KPH. Seeder dihentikan.');
            return;
        }

        // 2. Buat petak dummy per RPH (3 petak per RPH, total_pohon dipecah dari data rph)
        foreach ($rphList as $rph) {
            $totalPohon = (int) ($rph->jumlah_pohon ?? 0);
            if ($totalPohon <= 0) {
                continue;
            }

            $jumlahPetak = 3;
            $sisaPohon   = $totalPohon;

            for ($i = 1; $i <= $jumlahPetak; $i++) {
                $porsi = ($i === $jumlahPetak)
                    ? $sisaPohon
                    : (int) round($totalPohon / $jumlahPetak * random_int(85, 115) / 100);

                $porsi = max(1, min($porsi, $sisaPohon));
                $sisaPohon -= $porsi;

                DB::table('petak')->insert([
                    'id_rph'      => $rph->id_rph,
                    'kode_petak'  => $rph->kode_rph . '-P' . $i,
                    'total_pohon' => $porsi,
                    'latitude'    => null,
                    'longitude'   => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // 3. Target level BKPH (per bulan, oleh user KPH) - input jumlah pohon, persen dihitung otomatis
        foreach ($bkphList as $bkph) {
            $totalPohonBkph = (int) ($totalPohonPerBkph[$bkph->id_bkph] ?? 0);
            if ($totalPohonBkph <= 0) {
                continue;
            }

            foreach ($bulanList as $bulan) {
                $coverage           = random_int(75, 95); // KPH targetkan 75-95% dari total pohon
                $jumlahTargetPohon  = (int) round($totalPohonBkph * $coverage / 100);
                $targetPersen       = round($jumlahTargetPohon / $totalPohonBkph * 100, 2);

                DB::table('target')->insert([
                    'level_target'         => 'BKPH',
                    'id_bkph'              => $bkph->id_bkph,
                    'id_rph'               => null,
                    'id_user'              => $userKph->id_user,
                    'periode_bulan'        => $bulan,
                    'periode_tahun'        => $tahun,
                    'jumlah_target_pohon'  => $jumlahTargetPohon,
                    'target_persen'        => $targetPersen,
                    'status_periode'       => $this->statusPeriode($bulan, $bulanSekarang),
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            }
        }

        // 4. Target level RPH + Realisasi per petak (per bulan, oleh user KRPH masing-masing)
        foreach ($rphList as $rph) {
            $userKrph = DB::table('user')->where('id_rph', $rph->id_rph)->first();
            if (! $userKrph) {
                continue;
            }

            $petakList = DB::table('petak')->where('id_rph', $rph->id_rph)->get();
            $totalPohonRph = (int) ($rph->jumlah_pohon ?? 0);

            foreach ($bulanList as $bulan) {
                if ($totalPohonRph <= 0) {
                    continue;
                }

                $coverage          = random_int(70, 100); // KRPH targetkan 70-100% dari total pohon RPH
                $jumlahTargetPohon = (int) round($totalPohonRph * $coverage / 100);
                $targetPersen      = round($jumlahTargetPohon / $totalPohonRph * 100, 2);

                DB::table('target')->insert([
                    'level_target'        => 'RPH',
                    'id_bkph'             => null,
                    'id_rph'              => $rph->id_rph,
                    'id_user'             => $userKrph->id_user,
                    'periode_bulan'       => $bulan,
                    'periode_tahun'       => $tahun,
                    'jumlah_target_pohon' => $jumlahTargetPohon,
                    'target_persen'       => $targetPersen,
                    'status_periode'      => $this->statusPeriode($bulan, $bulanSekarang),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);

                // realisasi hanya dibuat untuk bulan yang sudah lewat / sedang berjalan
                if ($bulan > $bulanSekarang) {
                    continue;
                }

                $isBulanBerjalan = ($bulan === $bulanSekarang);

                foreach ($petakList as $petak) {
                    // di bulan berjalan (Juli), baru sebagian petak yang sempat lapor
                    // (baru tanggal 3, jadi ~40% kemungkinan sudah ada laporan)
                    if ($isBulanBerjalan && random_int(1, 100) > 40) {
                        continue;
                    }

                    if ($isBulanBerjalan) {
                        // capaian bulan berjalan masih kecil (baru awal bulan)
                        $capaianPersen = random_int(2, 10);
                        $tanggal       = sprintf('%d-%02d-%02d', $tahun, $bulan, random_int(1, $tanggalSekarang));
                    } else {
                        // bulan yang sudah lewat: capaian bervariasi di sekitar target
                        $capaianPersen = max(0, min(100, $targetPersen + random_int(-15, 10)));
                        $tanggal       = sprintf('%d-%02d-%02d', $tahun, $bulan, random_int(20, 28));
                    }

                    $jumlahRealisasi = (int) round($petak->total_pohon * $capaianPersen / 100);

                    DB::table('realisasi')->insert([
                        'id_petak'                => $petak->id_petak,
                        'id_mandor'               => null,
                        'id_user'                 => $userKrph->id_user,
                        'jumlah_pohon_realisasi'  => $jumlahRealisasi,
                        'total_pohon_petak'       => $petak->total_pohon,
                        'tanggal_update'          => $tanggal,
                        'catatan_lapangan'        => null,
                        'status_validasi'         => 'Valid',
                        'alasan_tidak_valid'      => null,
                        'versi_input'             => 1,
                        'id_realisasi_sebelumnya' => null,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ]);
                }
            }
        }

        $this->command->info('Dummy target & realisasi berhasil dibuat.');
    }

    private function statusPeriode(int $bulan, int $bulanSekarang): string
    {
        if ($bulan < $bulanSekarang) {
            return 'Berlalu';
        }

        if ($bulan === $bulanSekarang) {
            return 'Berjalan';
        }

        return 'Mendatang';
    }
}