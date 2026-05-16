<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Asisten Praktikum yang sudah disetujui & aktif bertugas.
 *
 * Tabel: asprak_praktikum (reuse tabel yang sudah ada)
 * Role: 'asprak' | 'koor'
 * Status: aktif | nonaktif
 */
class AsistenPraktikum extends Model
{
    use SoftDeletes;

    protected $table = 'asprak_praktikum';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'role',
        'deskripsi',
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

    public function modulAsprak()
    {
        return $this->hasMany(ModulAsprak::class, 'asprak_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeAsprak($query)
    {
        return $query->where('role', 'asprak');
    }

    public function scopeKoor($query)
    {
        return $query->where('role', 'koor');
    }
}
