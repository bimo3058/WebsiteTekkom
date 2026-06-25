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

    protected $table = 'pengumuman_praktikum';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'judul',
        'konten',
        'is_published',
        'tipe_sistem',
        'periode_id',
        'lampiran',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'lampiran' => 'array',
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