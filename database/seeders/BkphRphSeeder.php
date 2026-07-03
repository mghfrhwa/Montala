<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Data master BKPH & RPH — hasil rekonsiliasi antara Montala_db.sql (dump lama)
 * dengan Data_BKPH_RPH.pdf (laporan resmi pengiriman getah pinus ke PGT).
 *
 * Perubahan dari dump lama:
 * - BKPH "Pare" (PRE) DIHAPUS — tidak ada di laporan PGT, total 9 BKPH jadi 8.
 * - RPH "Prigi" (di bawah Bandung) DIHAPUS — tidak ada di laporan PGT.
 * - RPH "Pojok" (di bawah Kediri) DIHAPUS — tidak ada di laporan PGT.
 * - Typo "Pagerwejo" -> "Pagerwojo" (mengikuti ejaan resmi di laporan PGT).
 * - Kolom baru luas_produktif_ha & jumlah_pohon diisi dari laporan PGT (update 02/07/2026).
 *
 * CATATAN: total luas_ha di tabel `bkph` (107.832,73 Ha) TIDAK diubah karena sudah
 * cocok dengan angka di rancangan UI (8 BKPH * 36 RPH). Akibatnya jumlah luas_ha
 * RPH per BKPH tidak selalu pas 100% sama dengan luas_ha BKPH induknya (selisih
 * wilayah yang belum terpetakan ke RPH). Ini bukan bug — silakan koreksi manual
 * kalau KPH Kediri sudah punya angka luas administratif final per RPH.
 */
class BkphRphSeeder extends Seeder
{
    public function run(): void
    {
        $bkphData = [
            ['kode_bkph' => 'BKPH-KDR', 'nama_bkph' => 'Kediri',      'luas_ha' => 12653.70, 'latitude' => -7.8167000, 'longitude' => 111.9333000],
            ['kode_bkph' => 'BKPH-PAC', 'nama_bkph' => 'Pace',        'luas_ha' => 11614.14, 'latitude' => -7.7333000, 'longitude' => 111.9000000],
            ['kode_bkph' => 'BKPH-TGA', 'nama_bkph' => 'Tulungagung', 'luas_ha' => 14266.60, 'latitude' => -8.0667000, 'longitude' => 111.9000000],
            ['kode_bkph' => 'BKPH-BDG', 'nama_bkph' => 'Bandung',     'luas_ha' => 16376.45, 'latitude' => -8.1667000, 'longitude' => 111.7833000],
            ['kode_bkph' => 'BKPH-DNK', 'nama_bkph' => 'Dongko',      'luas_ha' => 14786.77, 'latitude' => -8.2167000, 'longitude' => 111.6833000],
            ['kode_bkph' => 'BKPH-KPK', 'nama_bkph' => 'Kampak',      'luas_ha' => 15453.90, 'latitude' => -8.2333000, 'longitude' => 111.6167000],
            ['kode_bkph' => 'BKPH-KRG', 'nama_bkph' => 'Karangan',    'luas_ha' => 10013.30, 'latitude' => -8.1000000, 'longitude' => 111.7333000],
            ['kode_bkph' => 'BKPH-TRK', 'nama_bkph' => 'Trenggalek',  'luas_ha' => 12667.87, 'latitude' => -8.0500000, 'longitude' => 111.7167000],
        ];

        // [kode_bkph, kode_rph, nama_rph, luas_ha (administratif, dari dump lama), luas_produktif_ha, jumlah_pohon]
        // 2 kolom terakhir sumbernya Data_BKPH_RPH.pdf (update 02/07/2026)
        $rphData = [
            ['BKPH-KDR', 'RPH-KDR-01', 'Kalipang',    2531.00, 278,  22716],
            ['BKPH-KDR', 'RPH-KDR-02', 'Kanyoran',    3484.70, 421,  56132],
            ['BKPH-KDR', 'RPH-KDR-03', 'Pamongan',    1411.30, 372,  51331],
            ['BKPH-KDR', 'RPH-KDR-04', 'Parang',      1776.60, 339,  26838],
            ['BKPH-KDR', 'RPH-KDR-05', 'Sambiroto',   2146.20, 843,  73552],

            ['BKPH-PAC', 'RPH-PAC-01', 'Bajulan',       1516.70, 252,  25974],
            ['BKPH-PAC', 'RPH-PAC-02', 'Gedangklutuk',  2281.80, 212,  19604],
            ['BKPH-PAC', 'RPH-PAC-03', 'Makuto',        2574.10, 522,  56879],
            ['BKPH-PAC', 'RPH-PAC-04', 'Plangkat',      1837.06,  31,   5693],
            ['BKPH-PAC', 'RPH-PAC-05', 'Salam Judeg',   1923.18, 387,  26694],
            ['BKPH-PAC', 'RPH-PAC-06', 'Sugihan',       1481.30, 373,  52646],

            ['BKPH-TGA', 'RPH-TGA-01', 'Gondang',      2807.10, 565,  88656],
            ['BKPH-TGA', 'RPH-TGA-02', 'Jatiwekas',    1861.20, 516,  52137],
            ['BKPH-TGA', 'RPH-TGA-03', 'Karangrejo',   2253.00, 769,  62695],
            ['BKPH-TGA', 'RPH-TGA-04', 'Pagerwojo',    5172.20, 1552, 175897],
            ['BKPH-TGA', 'RPH-TGA-05', 'Sendang',      2173.10, 588,  111181],

            ['BKPH-BDG', 'RPH-BDG-01', 'Bandung',   2308.70, 323, 10134],
            ['BKPH-BDG', 'RPH-BDG-02', 'Besuki',     4448.60, 119, 15436],
            ['BKPH-BDG', 'RPH-BDG-03', 'Watulimo',  4243.01, 184,  8858],

            ['BKPH-DNK', 'RPH-DNK-01', 'Banjar',           4065.65,  274,  14015],
            ['BKPH-DNK', 'RPH-DNK-02', 'Dongko Selatan',   2428.00, 1132,  83616],
            ['BKPH-DNK', 'RPH-DNK-03', 'Dongko Utara',     2160.00, 1163, 178623],
            ['BKPH-DNK', 'RPH-DNK-04', 'Panggul',          3456.12, 1356,  83576],
            ['BKPH-DNK', 'RPH-DNK-05', 'Sumberbening',     2676.30, 1872, 337519],

            ['BKPH-KPK', 'RPH-KPK-01', 'Kampak Selatan',    3313.80, 1374, 218282],
            ['BKPH-KPK', 'RPH-KPK-02', 'Kampak Utara',      2474.90, 1536, 289989],
            ['BKPH-KPK', 'RPH-KPK-03', 'Munjungan Barat',   3244.90,   63,   5973],
            ['BKPH-KPK', 'RPH-KPK-04', 'Munjungan Timur',   6420.30,  752,    866],

            ['BKPH-KRG', 'RPH-KRG-01', 'Gandusari',  2394.10,  482,  89977],
            ['BKPH-KRG', 'RPH-KRG-02', 'Karangan',   2669.60, 1402, 175543],
            ['BKPH-KRG', 'RPH-KRG-03', 'Pule',       2755.60, 1995, 253633],
            ['BKPH-KRG', 'RPH-KRG-04', 'Tugu',       2194.00, 1266,  96627],

            ['BKPH-TRK', 'RPH-TRK-01', 'Bendungan',   3740.69, 2225, 225388],
            ['BKPH-TRK', 'RPH-TRK-02', 'Durenan',     3191.50,  419,  13848],
            ['BKPH-TRK', 'RPH-TRK-03', 'Sumurup',     2207.40, 1545, 170004],
            ['BKPH-TRK', 'RPH-TRK-04', 'Trenggalek',  3528.28, 1879, 380308],
        ];

        DB::transaction(function () use ($bkphData, $rphData) {
            foreach ($bkphData as $b) {
                $idBkph = DB::table('bkph')->insertGetId([
                    'kode_bkph'  => $b['kode_bkph'],
                    'nama_bkph'  => $b['nama_bkph'],
                    'luas_ha'    => $b['luas_ha'],
                    'latitude'   => $b['latitude'],
                    'longitude'  => $b['longitude'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_bkph');

                foreach ($rphData as $r) {
                    if ($r[0] !== $b['kode_bkph']) {
                        continue;
                    }
                    DB::table('rph')->insert([
                        'id_bkph'           => $idBkph,
                        'kode_rph'          => $r[1],
                        'nama_rph'          => $r[2],
                        'luas_ha'           => $r[3],
                        'luas_produktif_ha' => $r[4],
                        'jumlah_pohon'      => $r[5],
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }
        });
    }
}
