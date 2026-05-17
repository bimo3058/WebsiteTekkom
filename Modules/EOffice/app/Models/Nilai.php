<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Nilai akhir per daftar_praktikan.
 *
 * Tabel: nilai_praktikum
 */
class Nilai extends Model
{
    protected $table = 'nilai_praktikum';

    protected $fillable = [
        'daftar_praktikan_id',
        'modul',
        'nilai_tugas',
        'nilai_absensi',
        'nilai_akhir',
        'disetujui_koor',
        'disetujui_dosen',
        'dipublikasikan',
    ];

    protected $casts = [
        'nilai_tugas'     => 'float',
        'nilai_absensi'   => 'float',
        'nilai_akhir'     => 'float',
        'disetujui_koor'  => 'boolean',
        'disetujui_dosen' => 'boolean',
        'dipublikasikan'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function daftarPraktikan()
    {
        return $this->belongsTo(DaftarPraktikan::class, 'daftar_praktikan_id');
    }
}
