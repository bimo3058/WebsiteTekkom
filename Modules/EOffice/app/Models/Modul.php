<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Modul / pertemuan dalam satu Praktikum.
 *
 * Tabel: modul_praktikum
 */
class Modul extends Model
{
    protected $table = 'modul_praktikum';

    protected $fillable = [
        'praktikum_id',
        'nama',
        'deskripsi',
        'urutan',
        'kode_modul',
        'jadwal_minggu',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function modulAsprak()
    {
        return $this->hasMany(ModulAsprak::class, 'modul_id');
    }

    public function asprak()
    {
        return $this->hasManyThrough(
            AsistenPraktikum::class,
            ModulAsprak::class,
            'modul_id',
            'id',
            'id',
            'asprak_id'
        );
    }

    public function materi()
    {
        return $this->hasMany(MateriModul::class, 'modul_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'modul_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'modul_id');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    /**
     * Generate kode unik 8 karakter untuk modul.
     */
    public static function generateKodeModul(): string
    {
        do {
            $kode = strtoupper(Str::random(8));
        } while (static::where('kode_modul', $kode)->exists());

        return $kode;
    }
}
