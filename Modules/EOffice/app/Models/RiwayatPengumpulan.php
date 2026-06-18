<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Riwayat pengumpulan tugas oleh mahasiswa (menyimpan riwayat file & revisi).
 *
 * Tabel: riwayat_pengumpulan_tugas
 */
class RiwayatPengumpulan extends Model
{
    protected $table = 'riwayat_pengumpulan_tugas';

    protected $fillable = [
        'pengumpulan_tugas_id',
        'file_path',
        'catatan',
        'is_revision',
    ];

    protected $casts = [
        'is_revision' => 'boolean',
    ];

    public function pengumpulanTugas()
    {
        return $this->belongsTo(PengumpulanTugas::class, 'pengumpulan_tugas_id');
    }
}
