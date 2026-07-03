<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    use HasFactory;

    protected $table = 'target';
    protected $primaryKey = 'id_target';

    protected $fillable = [
       'level_target', 'id_bkph', 'id_rph', 'id_user',
       'periode_bulan', 'periode_tahun',
       'jumlah_target_pohon', 'target_persen', 'status_periode',
   ];

    public function bkph(): BelongsTo
    {
        return $this->belongsTo(Bkph::class, 'id_bkph', 'id_bkph');
    }

    public function rph(): BelongsTo
    {
        return $this->belongsTo(Rph::class, 'id_rph', 'id_rph');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
