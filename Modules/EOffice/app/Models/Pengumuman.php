<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pengumuman per praktikum (dibuat oleh asprak/koor, atau otomatis oleh sistem).
 *
 * Tabel: pengumuman_praktikum
 *
 * tipe_sistem:
 *   null  = dibuat manual oleh koor/asprak
 *  'buka' = otomatis saat periode pendaftaran dibuka
 *  'tutup'= otomatis saat periode pendaftaran ditutup
 *
 * Soft delete dipakai agar pengumuman 'buka' yang sudah kadaluarsa
 * tidak tampil ke user, tapi masih tersimpan di riwayat admin.
 */
class Pengumuman extends Model
{
    use SoftDeletes;

    protected $table = 'eo_prak_pengumuman_praktikum';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'judul',
        'konten',
        'is_published',
        'tipe_sistem',
        'periode_id',
        'lampiran',
        'pinned_until',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'lampiran' => 'array',
        'pinned_until' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function isPinned(): bool
    {
        return $this->getAttributeFromArray('pinned_until') !== null && $this->pinned_until->isFuture();
    }

    public function scopePinnedFirst($query)
    {
        if(\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(),'pinned_until'))
            $query->orderByRaw('CASE WHEN pinned_until > ? THEN 0 ELSE 1 END',[now()]);
        return $query;
    }

    /** Expiry is evaluated at read time; no cron or deletion of old posts is required. */
    public function scopeForStudentDashboard($query, $userId)
    {
        $now = now();
        $pinsReady = \Illuminate\Support\Facades\Schema::hasColumn($this->getTable(),'pinned_until');
        $query->manual()->where('is_published',true)->where('created_at','<=',$now)
            ->whereHas('praktikum',fn($course)=>$course->where('is_active',true)
                ->whereHas('daftarPraktikan',fn($enrollment)=>$enrollment->where('user_id',$userId)))
            ->where(function($visible) use($now,$pinsReady){
                $visible->where('created_at','>=',$now->copy()->subDays(30));
                if($pinsReady)$visible->orWhere('pinned_until','>',$now);
            });
        if($pinsReady)$query->orderByRaw('CASE WHEN pinned_until > ? THEN 0 ELSE 1 END',[$now]);
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    /** Hanya pengumuman yang dibuat manual (bukan sistem). */
    public function scopeManual($query)
    {
        return $query->whereNull('tipe_sistem');
    }

    /** Hanya pengumuman sistem (buka/tutup). */
    public function scopeSistem($query)
    {
        return $query->whereNotNull('tipe_sistem');
    }

    /** Termasuk yang sudah soft-deleted — untuk riwayat admin. */
    public function scopeRiwayat($query)
    {
        return $query->withTrashed();
    }
}
