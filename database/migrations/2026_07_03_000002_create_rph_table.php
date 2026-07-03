<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rph', function (Blueprint $table) {
            $table->increments('id_rph');
            $table->unsignedInteger('id_bkph');
            $table->string('kode_rph', 20)->unique();
            $table->string('nama_rph', 100);
            $table->decimal('luas_ha', 12, 2)->default(0)
                ->comment('Luas wilayah kerja RPH (administratif)');

            // Kolom baru — hasil rekonsiliasi dengan Data_BKPH_RPH.pdf (laporan pengiriman getah pinus ke PGT)
            $table->decimal('luas_produktif_ha', 12, 2)->nullable()
                ->comment('Luas areal sadapan/produktif getah pinus per RPH, dari laporan PGT');
            $table->unsignedInteger('jumlah_pohon')->nullable()
                ->comment('Total pohon pinus tersadap di RPH ini, dari laporan PGT — dipakai sbg acuan/sanity-check target & total_pohon per petak');

            $table->timestamps();

            $table->foreign('id_bkph')->references('id_bkph')->on('bkph')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rph');
    }
};
