<?php

namespace Modules\EOffice\Models;

use App\Models\EoAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Praktikum extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'eo_praktikum';

    protected $fillable = [
        'nama',
        'kode',
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

    // ─── Boot: audit log pakai EoAuditLog global ──────────────────────────────

    protected static function booted(): void
    {
        static::created(fn($m)  => $m->writeAuditLog('create',  null, $m->getAttributes()));
        static::updated(fn($m)  => $m->writeAuditLog('update',  $m->getOriginal(), $m->getChanges()));
        static::deleted(fn($m)  => $m->writeAuditLog('delete',  $m->getAttributes(), null));
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
        } catch (\Throwable) {
            // Jangan crash app karena gagal audit log
        }
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    /**
     * Dosen pengampu — relasi ke User global superapp.
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /**
     * Koordinator praktikum — relasi ke User global superapp.
     */
    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koor_id');
    }
}
