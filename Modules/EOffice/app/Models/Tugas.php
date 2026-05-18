<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tugas per modul praktikum.
 *
 * Tabel: tugas_praktikum
 */
class Tugas extends Model
{
    protected $table = 'tugas_praktikum';

    protected $fillable = [
        'modul_id',
        'judul',
        'deskripsi',
        'deadline',
        'is_published',
    ];

    protected $casts = [
        'deadline'     => 'datetime',
        'is_published' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}
