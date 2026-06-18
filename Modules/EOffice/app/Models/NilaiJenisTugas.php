<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Nilai per jenis tugas per modul per praktikan.
 * Ini adalah sumber kebenaran tunggal untuk nilai Tugas Pendahuluan,
 * Praktikum, Laporan, dan Responsi — independen dari keberadaan form tugas.
 *
 * Tabel: nilai_jenis_tugas
 */
class NilaiJenisTugas extends Model
{
    protected $table = 'nilai_jenis_tugas';

    /**
     * Jenis tugas yang didukung.
     */
    const JENIS = [
        'tugas_pendahuluan' => 'Tugas Pendahuluan',
        'praktikum'         => 'Praktikum',
        'laporan'           => 'Laporan',
        'responsi'          => 'Responsi',
    ];

    protected $fillable = [
        'daftar_praktikan_id',
        'modul_id',
        'jenis_tugas',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function daftarPraktikan()
    {
        return $this->belongsTo(DaftarPraktikan::class, 'daftar_praktikan_id');
    }

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }
}
