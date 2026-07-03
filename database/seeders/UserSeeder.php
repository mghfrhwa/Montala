<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1 akun KPH (akses semua wilayah)
        DB::table('user')->insert([
            'nama'         => 'KPH Kediri',
            'username'     => 'kph.kediri',
            'password'     => Hash::make('kph1234'),
            'role'         => 'KPH',
            'id_rph'       => null,
            'status_aktif' => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 1 contoh akun KRPH/Asper, di-attach ke RPH pertama (Kalipang)
        $idRphKalipang = DB::table('rph')->where('kode_rph', 'RPH-KDR-01')->value('id_rph');

        DB::table('user')->insert([
            'nama'         => 'Asper Kalipang',
            'username'     => 'asper.kalipang',
            'password'     => Hash::make('kalipunk'),
            'role'         => 'KRPH',
            'id_rph'       => $idRphKalipang,
            'status_aktif' => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
