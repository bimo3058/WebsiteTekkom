<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Distribusi Asisten Praktikum ke Modul.
 *
 * Tabel: modul_asprak
 */
class ModulAsprak extends Model
{
    protected $table = 'modul_asprak';

    protected $fillable = [
        'modul_id',
        'asprak_id',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function asprak()
    {
        return $this->belongsTo(AsistenPraktikum::class, 'asprak_id');
    }
}
