<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi_foto', function (Blueprint $table) {
            $table->increments('id_foto');
            $table->unsignedInteger('id_realisasi');
            $table->string('path_foto', 255);
            $table->string('keterangan', 255)->nullable()
                ->comment('Contoh: "Foto sampel pohon" / "Foto Buku Saku Mandor"');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Disiapkan untuk GPS lapangan');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('tanggal_upload')->useCurrent();

            $table->foreign('id_realisasi')->references('id_realisasi')->on('realisasi')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_foto');
    }
};
