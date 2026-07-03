<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Realisasi extends Model
{
    use HasFactory;

    protected $table = 'realisasi';
    protected $primaryKey = 'id_realisasi';

    protected $fillable = [
        'id_petak', 'id_mandor', 'id_user',
        'jumlah_pohon_realisasi', 'total_pohon_petak',
        'tanggal_update', 'catatan_lapangan',
        'status_validasi', 'alasan_tidak_valid',
        'versi_input', 'id_realisasi_sebelumnya',
    ];

    // persentase_capaian TIDAK boleh diisi manual — itu generated column MySQL (STORED),
    // dihitung otomatis dari jumlah_pohon_realisasi / total_pohon_petak.
    protected $guarded_computed = ['persentase_capaian'];

    protected $casts = [
        'tanggal_update'      => 'date',
        'persentase_capaian'  => 'decimal:2',
    ];

    public function petak(): BelongsTo
    {
        return $this->belongsTo(Petak::class, 'id_petak', 'id_petak');
    }

    public function mandor(): BelongsTo
    {
        return $this->belongsTo(Mandor::class, 'id_mandor', 'id_mandor');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function versiSebelumnya(): BelongsTo
    {
        return $this->belongsTo(Realisasi::class, 'id_realisasi_sebelumnya', 'id_realisasi');
    }

    public function dokumentasiFoto(): HasMany
    {
        return $this->hasMany(DokumentasiFoto::class, 'id_realisasi', 'id_realisasi');
    }

    public function scopeValid($query)
    {
        return $query->where('status_validasi', 'Valid');
    }

    public function getStatusWarnaAttribute(): string
    {
        return match (true) {
            $this->persentase_capaian >= 80 => 'hijau',
            $this->persentase_capaian >= 40 => 'kuning',
            default => 'merah',
        };
    }
}
