<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pengumpulan tugas oleh mahasiswa.
 *
 * Tabel: pengumpulan_tugas
 * status_pengumpulan: belum_dicek | revisi | acc
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
        'status_pengumpulan',
    ];

    protected $casts = [
        'nilai'              => 'float',
        'is_revision'        => 'boolean',
    ];

    // Status constants
    const STATUS_BELUM_DICEK = 'belum_dicek';
    const STATUS_REVISI      = 'revisi';
    const STATUS_ACC         = 'acc';

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
