<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mandor extends Model
{
    use HasFactory;

    protected $table = 'mandor';
    protected $primaryKey = 'id_mandor';
    public $timestamps = false; // hanya created_at di skema asli

    protected $fillable = [
        'id_rph', 'nama_mandor', 'alur_kerja', 'status_aktif', 'created_at',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'created_at'   => 'datetime',
    ];

    public function rph(): BelongsTo
    {
        return $this->belongsTo(Rph::class, 'id_rph', 'id_rph');
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(Realisasi::class, 'id_mandor', 'id_mandor');
    }
}
