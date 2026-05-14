<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Pendaftaran calon Koordinator Asisten Praktikum.
 *
 * Tabel: pendaftaran_koordinator
 * Status: pending | approved | rejected
 */
class PendaftaranKoordinator extends Model
{
    protected $table = 'pendaftaran_koordinator';

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'ipk',
        'motivasi',
        'status',
        'alasan_penolakan',
    ];

    protected $casts = [
        'ipk' => 'float',
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

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
