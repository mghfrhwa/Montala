<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('id_user');
            $table->string('aktivitas', 255);
            $table->timestamp('tanggal_waktu')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('user')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
