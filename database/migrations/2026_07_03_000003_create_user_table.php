<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->enum('role', ['KPH', 'KRPH']);
            $table->unsignedInteger('id_rph')->nullable()
                ->comment('Wajib diisi jika role = KRPH. NULL utk role KPH (akses seluruh KPH Kediri)');
            $table->boolean('status_aktif')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_rph')->references('id_rph')->on('rph')->nullOnDelete()->cascadeOnUpdate();
        });

        // padanan CHECK constraint di dump asli: role=KRPH wajib id_rph, role=KPH id_rph boleh null
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE `user` ADD CONSTRAINT chk_user_role_wilayah CHECK (role = 'KRPH' and id_rph is not null or role = 'KPH')"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
