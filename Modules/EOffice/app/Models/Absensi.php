<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Absensi per modul per praktikan.
 *
 * Tabel: absensi_praktikum
 * status: hadir | izin | tidak_hadir
 */
class Absensi extends Model
{
    protected $table = 'absensi_praktikum';

    protected $fillable = [
        'modul_id',
        'daftar_praktikan_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function daftarPraktikan()
    {
        return $this->belongsTo(DaftarPraktikan::class, 'daftar_praktikan_id');
    }
}
