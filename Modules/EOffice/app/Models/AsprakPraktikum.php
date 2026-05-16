<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsprakPraktikum extends Model
{
    use SoftDeletes;

    protected $table = 'asprak_praktikum';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'role',       // 'asprak' | 'koor'
        'deskripsi',
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

    public function scopeAsprak($query)
    {
        return $query->where('role', 'asprak');
    }

    public function scopeKoor($query)
    {
        return $query->where('role', 'koor');
    }
}
