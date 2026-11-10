<?php

namespace Modules\EOffice\Models;

use App\Models\EoAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Praktikum extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'eo_prak_praktikum';

    public function laporanLabel(): string
    {
        return Str::contains(Str::lower($this->nama ?? ''), 'multimedia') ? 'Lembar Analisis' : 'Laporan';
    }

    public function moduleTaskTypes(): array
    {
        $types = ['tugas_pendahuluan'=>'Tugas Pendahuluan', 'laporan'=>$this->laporanLabel(), 'responsi'=>'Responsi', 'tugas_pengganti'=>'Tugas Pengganti'];
        foreach ($this->komponen_modul ?? [] as $component) $types[$component['id']] = $component['nama'];
        return $types;
    }

    public function moduleWeights(): array
    {
        $weights = ['tugas_pendahuluan'=>(int)($this->bobot_tp ?? 10), 'tugas_pengganti'=>(int)($this->bobot_praktikum ?? 30), 'laporan'=>(int)($this->bobot_laporan ?? 30), 'responsi'=>(int)($this->bobot_responsi ?? 30)];
        foreach ($this->komponen_modul ?? [] as $component) $weights[$component['id']] = $component['bobot'];
        return $weights;
    }

    public function moduleWeightsValid(): bool
    {
        return array_sum($this->moduleWeights()) === 100 && collect($this->moduleWeights())->every(fn ($weight) => $weight !== null && is_numeric($weight) && (float)$weight === (float)(int)$weight && $weight >= 0 && $weight <= 100);
    }

    public function moduleAverage(array $scores): ?float
    {
        if (!$this->moduleWeightsValid()) return null;
        return collect($this->moduleWeights())->map(fn ($weight,$key) => (float)($scores[$key] ?? 0) * $weight / 100)->sum();
    }

    public function moduleComponentsLocked(): bool
    {
        if ($this->komponen_modul_locked_at) return true;
        $ids = Modul::where('praktikum_id',$this->id)->pluck('id');
        return Tugas::whereIn('modul_id',$ids)->where('is_published',true)->exists()
            || NilaiJenisTugas::whereIn('modul_id',$ids)->whereNotNull('nilai')->exists()
            || PengumpulanTugas::whereHas('tugas',fn ($q) => $q->whereIn('modul_id',$ids))->exists();
    }

    public function moduleWeightsLocked(): bool
    {
        return Nilai::whereIn('daftar_praktikan_id',DaftarPraktikan::where('praktikum_id',$this->id)->select('id'))
            ->where(fn ($q)=>$q->where('disetujui_koor',true)->orWhere('disetujui_dosen',true)->orWhere('dipublikasikan',true))->exists();
    }

    protected $fillable = [
        'nama',
        'kode',
        'matkul_id',
        'deskripsi',
        'cover_path',
        'koor_id',
        'tahun_ajaran',
        'semester',
        'status',
        'status_pelaksanaan',
        'started_at',
        'jumlah_kelompok',
        'jumlah_shift',
        'metode_pembagian',
        'draft_pembagian',
        'bobot_tp',
        'bobot_praktikum',
        'bobot_laporan',
        'bobot_responsi',
        'bobot_gabungan_modul',
        'bobot_gabungan_ta',
        'bobot_gabungan_version',
        'final_grade_batch_id',
        'final_components',
        'komponen_modul',
        'komponen_modul_locked_at',
        'komponen_modul_version',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at'   => 'datetime',
            'is_active'    => 'boolean',
            'draft_pembagian' => 'array',
            'started_at' => 'datetime',
            'komponen_modul' => 'array',
            'final_components' => 'array',
            'komponen_modul_locked_at' => 'datetime',
            'komponen_modul_version' => 'integer',
        ];
    }

    // ─── Boot: audit log ──────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::created(fn($m) => $m->writeAuditLog('create', null, $m->getAttributes()));
        static::updated(fn($m) => $m->writeAuditLog('update', $m->getOriginal(), $m->getChanges()));
        static::deleted(fn($m) => $m->writeAuditLog('delete', $m->getAttributes(), null));
    }

    private function writeAuditLog(string $action, ?array $old, ?array $new): void
    {
        try {
            if (!Auth::id()) return;
            // A savepoint keeps a failed audit from invalidating an enclosing approval transaction.
            DB::transaction(fn () => EoAuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'subject_type' => 'Praktikum',
                // The audit subject_id column is numeric; retain the UUID in the payload.
                'description' => 'Perubahan praktikum '.$this->getKey(),
                'old_data' => $old,
                'new_data' => array_merge($new ?? [], ['id' => $this->getKey()]),
                'created_at' => now(),
            ]));
        } catch (\Throwable) {}
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function matkul()
    {
        return $this->belongsTo(MatkulPraktikum::class, 'matkul_id');
    }

    public function dosens()
    {
        return $this->belongsToMany(User::class, 'eo_prak_praktikum_dosen', 'praktikum_id', 'dosen_id');
    }

    /**
     * Backward compatibility accessor for $praktikum->dosen
     */
    public function getDosenAttribute()
    {
        return $this->dosens->first();
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koor_id');
    }

    public function daftarPraktikan()
    {
        return $this->hasMany(DaftarPraktikan::class, 'praktikum_id');
    }

    public function asprakPraktikum()
    {
        return $this->hasMany(AsprakPraktikum::class, 'praktikum_id');
    }

    public function asistenPraktikum()
    {
        return $this->hasMany(AsprakPraktikum::class, 'praktikum_id')->where('role', 'asprak');
    }

    public function modul()
    {
        return $this->hasMany(Modul::class, 'praktikum_id');
    }

    public function pengajuanModul()
    {
        return $this->hasMany(PengajuanModul::class, 'praktikum_id');
    }

    public function periodePendaftaran()
    {
        return $this->hasMany(PeriodePendaftaran::class, 'praktikum_id');
    }

    public function pendaftaranPraktikan()
    {
        return $this->hasMany(PendaftaranPraktikan::class, 'praktikum_id');
    }

    /**
     * Relasi ke periode pendaftaran yang sedang aktif & dalam rentang waktu buka.
     * Dipakai oleh whereHas('periodeAktif') di controller mahasiswa.
     */
    public function periodeAktif()
    {
        $now = now();
        return $this->hasMany(PeriodePendaftaran::class, 'praktikum_id')
            ->where('is_aktif', true)
            ->where(fn($q) => $q->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', $now))
            ->where(fn($q) => $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>', $now));
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    public static function generateKode(): string
    {
        do {
            $kode = strtoupper(Str::random(6));
        } while (static::where('kode', $kode)->exists());

        return $kode;
    }
}
