<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BkphRphSeeder::class, // bkph & rph harus lebih dulu (di-refer tabel lain)
            UserSeeder::class,    // butuh id_rph dari seeder di atas
        ]);
    }
}
