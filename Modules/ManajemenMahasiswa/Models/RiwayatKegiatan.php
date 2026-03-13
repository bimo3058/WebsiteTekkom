<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatKegiatan extends Model
{
    use HasFactory;

    protected $table = 'mk_riwayat_kegiatan';

    protected $fillable = [
        'student_id',
        'kegiatan_id',
        'peran',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const PERAN_KETUA     = 'ketua';
    const PERAN_ANGGOTA   = 'anggota';
    const PERAN_PANITIA   = 'panitia';
    const PERAN_PESERTA   = 'peserta';

    const PERAN_LIST = [
        self::PERAN_KETUA,
        self::PERAN_ANGGOTA,
        self::PERAN_PANITIA,
        self::PERAN_PESERTA,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByPeran($query, string $peran)
    {
        return $query->where('peran', $peran);
    }

    public function scopeByStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}