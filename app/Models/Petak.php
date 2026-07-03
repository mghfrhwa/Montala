<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petak extends Model
{
    use HasFactory;

    protected $table = 'petak';
    protected $primaryKey = 'id_petak';

    protected $fillable = [
        'id_rph', 'kode_petak', 'total_pohon', 'latitude', 'longitude',
    ];

    public function rph(): BelongsTo
    {
        return $this->belongsTo(Rph::class, 'id_rph', 'id_rph');
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(Realisasi::class, 'id_petak', 'id_petak');
    }

    /** Realisasi versi terbaru yang valid untuk petak ini (dipakai di dashboard/progres). */
    public function realisasiTerbaru()
    {
        return $this->realisasi()->latest('tanggal_update')->latest('versi_input')->first();
    }
}
