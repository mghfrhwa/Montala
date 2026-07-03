<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi', function (Blueprint $table) {
            $table->increments('id_realisasi');
            $table->unsignedInteger('id_petak');
            $table->unsignedInteger('id_mandor')->nullable();
            $table->unsignedInteger('id_user')->comment('KRPH/Asper yang menginput');
            $table->unsignedInteger('jumlah_pohon_realisasi');
            $table->unsignedInteger('total_pohon_petak');
            $table->date('tanggal_update');
            $table->text('catatan_lapangan')->nullable();
            $table->enum('status_validasi', ['Menunggu', 'Valid', 'Tidak Valid'])->default('Menunggu');
            $table->string('alasan_tidak_valid', 255)->nullable()
                ->comment('Contoh: "Tanpa dokumentasi foto"');
            $table->unsignedInteger('versi_input')->default(1)
                ->comment('Naik tiap kali data diperbaiki ulang');
            $table->unsignedInteger('id_realisasi_sebelumnya')->nullable()
                ->comment('Referensi ke versi sebelumnya jika ini hasil perbaikan');
            $table->timestamps();

            $table->foreign('id_petak')->references('id_petak')->on('petak')->cascadeOnUpdate();
            $table->foreign('id_mandor')->references('id_mandor')->on('mandor')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('id_user')->references('id_user')->on('user')->cascadeOnUpdate();
            $table->foreign('id_realisasi_sebelumnya')->references('id_realisasi')->on('realisasi')->nullOnDelete()->cascadeOnUpdate();
        });

        // generated column persis seperti dump asli — dihitung otomatis oleh MySQL, jangan pernah diisi manual dari Eloquent
        DB::statement("
            ALTER TABLE `realisasi`
            ADD COLUMN `persentase_capaian` DECIMAL(5,2)
            GENERATED ALWAYS AS (
                CASE WHEN `total_pohon_petak` > 0
                     THEN ROUND(`jumlah_pohon_realisasi` / `total_pohon_petak` * 100, 2)
                     ELSE 0 END
            ) STORED
            AFTER `total_pohon_petak`
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi');
    }
};
