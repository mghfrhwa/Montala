<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petak', function (Blueprint $table) {
            $table->increments('id_petak');
            $table->unsignedInteger('id_rph');
            $table->string('kode_petak', 20);
            $table->unsignedInteger('total_pohon')->nullable()
                ->comment('Perkiraan jumlah pohon di petak, bisa dioverride saat input realisasi');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['id_rph', 'kode_petak'], 'uq_petak_per_rph');
            $table->foreign('id_rph')->references('id_rph')->on('rph')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petak');
    }
};
