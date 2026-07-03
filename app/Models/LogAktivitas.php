<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = ['id_user', 'aktivitas', 'tanggal_waktu'];

    protected $casts = ['tanggal_waktu' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /** Helper cepat: LogAktivitas::catat($userId, "Input realisasi petak 12b"); */
    public static function catat(int $idUser, string $aktivitas): self
    {
        return static::create([
            'id_user'       => $idUser,
            'aktivitas'     => $aktivitas,
            'tanggal_waktu' => now(),
        ]);
    }
}
