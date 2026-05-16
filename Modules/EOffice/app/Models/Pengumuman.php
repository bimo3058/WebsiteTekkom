<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Pengumuman dalam Praktikum.
 *
 * Tabel: pengumuman_praktikum
 */
class Pengumuman extends Model
{
    protected $table = 'pengumuman_praktikum';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'judul',
        'konten',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
