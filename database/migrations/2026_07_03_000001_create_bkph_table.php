<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bkph', function (Blueprint $table) {
            $table->increments('id_bkph');
            $table->string('kode_bkph', 20)->unique();
            $table->string('nama_bkph', 100);
            $table->decimal('luas_ha', 12, 2)->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bkph');
    }
};
