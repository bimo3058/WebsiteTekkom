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
        'nama_kegiatan_manual',
        'peran_manual',
        'tanggal_kegiatan',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
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
    // Accessors — unified nama & peran (auto-resolve manual vs linked)
    // -------------------------------------------------------------------------

    /**
     * Nama kegiatan: prioritas dari relasi mk_kegiatan, fallback ke manual.
     */
    public function getNamaKegiatanAttribute(): string
    {
        if ($this->kegiatan) {
            return $this->kegiatan->judul;
        }
        return $this->nama_kegiatan_manual ?? 'Kegiatan tidak ditemukan';
    }

    /**
     * Peran: prioritas dari peran_manual jika ada, lalu peran bawaan.
     */
    public function getPeranLabelAttribute(): string
    {
        if ($this->peran_manual) {
            return $this->peran_manual;
        }
        return ucfirst($this->peran ?? '');
    }

    /**
     * Apakah ini entri manual (bukan dari list kegiatan)?
     */
    public function getIsManualAttribute(): bool
    {
        return is_null($this->kegiatan_id);
    }

    /**
     * Tanggal untuk ditampilkan: dari kegiatan (jika ada) atau tanggal_kegiatan manual.
     */
    public function getTanggalDisplayAttribute()
    {
        if ($this->kegiatan && $this->kegiatan->tanggal_mulai) {
            return $this->kegiatan->tanggal_mulai;
        }
        return $this->tanggal_kegiatan;
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