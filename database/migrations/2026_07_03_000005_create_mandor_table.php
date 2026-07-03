<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mandor', function (Blueprint $table) {
            $table->increments('id_mandor');
            $table->unsignedInteger('id_rph');
            $table->string('nama_mandor', 100);
            $table->string('alur_kerja', 10)->comment('Contoh: Alur A, Alur B, Alur C');
            $table->boolean('status_aktif')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_rph')->references('id_rph')->on('rph')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mandor');
    }
};
