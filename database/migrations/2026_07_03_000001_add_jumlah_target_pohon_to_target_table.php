<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_target_pohon')
                ->nullable()
                ->after('periode_tahun');
        });
    }

    public function down(): void
    {
        Schema::table('target', function (Blueprint $table) {
            $table->dropColumn('jumlah_target_pohon');
        });
    }
};