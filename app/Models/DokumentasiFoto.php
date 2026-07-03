<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumentasiFoto extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi_foto';
    protected $primaryKey = 'id_foto';
    public $timestamps = false; // hanya tanggal_upload di skema asli

    protected $fillable = [
        'id_realisasi', 'path_foto', 'keterangan', 'latitude', 'longitude', 'tanggal_upload',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    public function realisasi(): BelongsTo
    {
        return $this->belongsTo(Realisasi::class, 'id_realisasi', 'id_realisasi');
    }

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->path_foto);
    }
}
