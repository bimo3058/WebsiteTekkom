<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pengumpulan tugas oleh mahasiswa.
 *
 * Tabel: pengumpulan_tugas
 */
class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'daftar_praktikan_id',
        'file_path',
        'catatan',
        'nilai',
        'catatan_revisi',
        'is_revision',
    ];

    protected $casts = [
        'nilai'       => 'float',
        'is_revision' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function daftarPraktikan()
    {
        return $this->belongsTo(DaftarPraktikan::class, 'daftar_praktikan_id');
    }
}
