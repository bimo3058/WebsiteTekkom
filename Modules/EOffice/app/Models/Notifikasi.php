<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Notifikasi untuk user (surat, peminjaman, praktikum, dsb).
 *
 * Tabel: eo_notifikasi
 */
class Notifikasi extends Model
{
    protected $table = 'eo_notifikasi';

    protected $fillable = [
        'user_id',
        'peminjaman_id',
        'judul',
        'pesan',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}