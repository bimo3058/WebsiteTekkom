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
    protected static function booted(): void
    {
        static::saving(function (self $task) {
            $modul=Modul::findOrFail($task->modul_id);
            \Illuminate\Support\Facades\DB::transaction(function () use ($task,$modul) {
                $praktikum=Praktikum::whereKey($modul->praktikum_id)->lockForUpdate()->firstOrFail();
                if (!array_key_exists($task->jenis_tugas,$praktikum->moduleTaskTypes())) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['jenis_tugas'=>'Komponen penilaian tidak tersedia. Muat ulang halaman.']);
                }
                if ($task->exists && $task->isDirty('jenis_tugas') && $praktikum->moduleComponentsLocked()) throw \Illuminate\Validation\ValidationException::withMessages(['jenis_tugas'=>'Jenis tugas tidak dapat diubah setelah tugas modul dibuka.']);
                if ($task->is_published) {
                    if (!$praktikum->moduleWeightsValid()) throw \Illuminate\Validation\ValidationException::withMessages(['bobot'=>'Lengkapi bobot seluruh komponen dengan total 100% sebelum membuka tugas.']);
                    if (!$praktikum->komponen_modul_locked_at) $praktikum->update(['komponen_modul_locked_at'=>now()]);
                }
            });
        });
    }
    protected $table = 'tugas_praktikum';

    /**
     * Jenis tugas yang tersedia.
     */
    const JENIS = [
        'tugas_pendahuluan' => 'Tugas Pendahuluan',
        'laporan' => 'Laporan',
        'responsi' => 'Responsi',
        'tugas_pengganti' => 'Tugas Pengganti',
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
        'deadline' => 'datetime',
        'deadline_acc' => 'datetime',
        'is_published' => 'boolean',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Label tampilan untuk jenis_tugas.
     */
    public function jenisLabel(): string
    {
        $praktikum=Praktikum::whereHas('modul',fn ($q)=>$q->where('id',$this->modul_id))->first();
        return $praktikum?->moduleTaskTypes()[$this->jenis_tugas] ?? self::JENIS[$this->jenis_tugas] ?? ucfirst($this->jenis_tugas ?? '-');
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
