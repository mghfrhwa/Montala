<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama', 'username', 'password', 'role', 'id_rph', 'status_aktif',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    /**
     * Laravel auth secara default cari kolom 'email' untuk login.
     * Karena SITALANG login pakai username, override ini di LoginController
     * (Auth::attempt(['username' => ..., 'password' => ...])) — tidak perlu
     * diubah di sini, cukup pastikan kolom 'username' ada & unique (sudah).
     */
    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function rph(): BelongsTo
    {
        return $this->belongsTo(Rph::class, 'id_rph', 'id_rph');
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(Realisasi::class, 'id_user', 'id_user');
    }

    public function target(): HasMany
    {
        return $this->hasMany(Target::class, 'id_user', 'id_user');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_user', 'id_user');
    }

    public function isKph(): bool
    {
        return $this->role === 'KPH';
    }

    public function isKrph(): bool
    {
        return $this->role === 'KRPH';
    }
}
