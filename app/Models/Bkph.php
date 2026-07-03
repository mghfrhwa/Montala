<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bkph extends Model
{
    use HasFactory;

    protected $table = 'bkph';
    protected $primaryKey = 'id_bkph';

    protected $fillable = [
        'kode_bkph', 'nama_bkph', 'luas_ha', 'latitude', 'longitude',
    ];

    protected $casts = [
        'luas_ha'   => 'decimal:2',
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function rph(): HasMany
    {
        return $this->hasMany(Rph::class, 'id_bkph', 'id_bkph');
    }

    public function target(): HasMany
    {
        return $this->hasMany(Target::class, 'id_bkph', 'id_bkph');
    }
}
