<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengurusHimaskom extends Model
{
    use HasFactory;

    protected $table = 'mk_pengurus_himaskom';

    protected $fillable = [
        'student_id',
        'kepengurusan_id',
        'divisi',
        'jabatan',
        'status_keaktifan',
    ];

    // -------------------------------------------------------------------------
    // Constants — hindari magic string di seluruh codebase
    // -------------------------------------------------------------------------

    const STATUS_AKTIF    = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';
    const STATUS_CUTI     = 'cuti';

    const STATUS_LIST = [
        self::STATUS_AKTIF,
        self::STATUS_NONAKTIF,
        self::STATUS_CUTI,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Relasi ke tabel students (lintas modul — gunakan string class jika
     * model Student berada di modul lain, e.g. App\Models\Student)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function kepengurusan(): BelongsTo
    {
        return $this->belongsTo(Kepengurusan::class, 'kepengurusan_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeAktif($query)
    {
        return $query->where('status_keaktifan', self::STATUS_AKTIF);
    }

    public function scopeByDivisi($query, string $divisi)
    {
        return $query->where('divisi', $divisi);
    }

    public function scopeByKepengurusan($query, int $kepengurusanId)
    {
        return $query->where('kepengurusan_id', $kepengurusanId);
    }
}