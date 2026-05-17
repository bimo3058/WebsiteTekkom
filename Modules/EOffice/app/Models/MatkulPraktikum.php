<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mata kuliah yang memiliki komponen praktikum.
 * Data sesuai Kurikulum 2024 S1 Teknik Komputer UNDIP.
 *
 * Tabel: eo_matkul_praktikum
 */
class MatkulPraktikum extends Model
{
    protected $table = 'eo_matkul_praktikum';

    protected $fillable = [
        'kode',
        'nama',
        'sks',
        'semester',
    ];

    protected $casts = [
        'sks'      => 'integer',
        'semester' => 'integer',
    ];

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeBySemester($query, int $semester)
    {
        return $query->where('semester', $semester);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function getLabelAttribute(): string
    {
        return "{$this->kode} — {$this->nama} ({$this->sks} SKS)";
    }

    // ── Relationships ───────────────────────────────────────────────────────

    /**
     * Praktikum yang menggunakan matkul ini.
     */
    public function praktikums()
    {
        return $this->hasMany(Praktikum::class, 'matkul_id');
    }
}
