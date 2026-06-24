<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tugas per modul praktikum.
 *
 * Tabel: tugas_praktikum
 */
class Tugas extends Model
{
    protected $table = 'tugas_praktikum';

    /**
     * Jenis tugas yang tersedia.
     */
    const JENIS = [
        'tugas_pendahuluan' => 'Tugas Pendahuluan',
        'praktikum'         => 'Praktikum',
        'laporan'           => 'Laporan',
        'responsi'          => 'Responsi',
    ];

    protected $fillable = [
        'modul_id',
        'jenis_tugas',
        'judul',
        'deskripsi',
        'deadline',
        'deadline_acc',
        'is_published',
        'file_path',
    ];

    protected $casts = [
        'deadline'     => 'datetime',
        'deadline_acc' => 'datetime',
        'is_published' => 'boolean',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Label tampilan untuk jenis_tugas.
     */
    public function jenisLabel(): string
    {
        return self::JENIS[$this->jenis_tugas] ?? ucfirst($this->jenis_tugas ?? '-');
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}
