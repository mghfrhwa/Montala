<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target', function (Blueprint $table) {
            $table->increments('id_target');
            $table->enum('level_target', ['BKPH', 'RPH']);
            $table->unsignedInteger('id_bkph')->nullable()->comment('Diisi jika level_target = BKPH');
            $table->unsignedInteger('id_rph')->nullable()->comment('Diisi jika level_target = RPH');
            $table->unsignedInteger('id_user')->comment('KPH yang menetapkan target');
            $table->unsignedTinyInteger('periode_bulan')->comment('1-12');
            $table->unsignedSmallInteger('periode_tahun');
            $table->decimal('target_persen', 5, 2)->comment('Contoh: 70.00 (%)');
            $table->enum('status_periode', ['Berlalu', 'Berjalan', 'Mendatang'])->default('Mendatang');
            $table->timestamps();

            $table->unique(['id_bkph', 'periode_bulan', 'periode_tahun'], 'uq_target_bkph_periode');
            $table->unique(['id_rph', 'periode_bulan', 'periode_tahun'], 'uq_target_rph_periode');
            $table->foreign('id_bkph')->references('id_bkph')->on('bkph')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_rph')->references('id_rph')->on('rph')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_user')->references('id_user')->on('user')->cascadeOnUpdate();
        });

        DB::statement("
            ALTER TABLE `target` ADD CONSTRAINT chk_target_level CHECK (
                (level_target = 'BKPH' AND id_bkph IS NOT NULL AND id_rph IS NULL)
                OR
                (level_target = 'RPH' AND id_rph IS NOT NULL AND id_bkph IS NULL)
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('target');
    }
};
