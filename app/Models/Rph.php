<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rph extends Model
{
    use HasFactory;

    protected $table = 'rph';
    protected $primaryKey = 'id_rph';

    protected $fillable = [
        'id_bkph', 'kode_rph', 'nama_rph', 'luas_ha',
        'luas_produktif_ha', 'jumlah_pohon',
    ];

    protected $casts = [
        'luas_ha'           => 'decimal:2',
        'luas_produktif_ha' => 'decimal:2',
        'jumlah_pohon'      => 'integer',
    ];

    public function bkph(): BelongsTo
    {
        return $this->belongsTo(Bkph::class, 'id_bkph', 'id_bkph');
    }

    public function petak(): HasMany
    {
        return $this->hasMany(Petak::class, 'id_rph', 'id_rph');
    }

    public function mandor(): HasMany
    {
        return $this->hasMany(Mandor::class, 'id_rph', 'id_rph');
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'id_rph', 'id_rph');
    }

    public function target(): HasMany
    {
        return $this->hasMany(Target::class, 'id_rph', 'id_rph');
    }

    /**
     * Total pohon aktual yang sudah tercatat lewat realisasi (semua petak di RPH ini).
     * Bisa dibandingkan dengan jumlah_pohon (acuan dari laporan PGT) sbg sanity-check.
     */
    public function totalRealisasiPohon(): int
    {
        return Realisasi::whereIn('id_petak', $this->petak()->pluck('id_petak'))
            ->where('status_validasi', 'Valid')
            ->sum('jumlah_pohon_realisasi');
    }
}
