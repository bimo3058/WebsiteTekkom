<?php

namespace Modules\EOffice\Models;

use App\Models\EoAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Praktikum extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'eo_praktikum';

    protected $fillable = [
        'nama',
        'kode',
        'matkul_id',
        'deskripsi',
        'dosen_id',
        'koor_id',
        'tahun_ajaran',
        'semester',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tahun_ajaran' => 'integer',
            'deleted_at'   => 'datetime',
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
            EoAuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'model'      => 'Praktikum',
                'model_id'   => $this->getKey(),
                'old_values' => $old,
                'new_values' => $new,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable) {}
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function matkul()
    {
        return $this->belongsTo(MatkulPraktikum::class, 'matkul_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
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