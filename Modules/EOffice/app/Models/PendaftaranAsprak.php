<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Pendaftaran calon Asisten Praktikum oleh mahasiswa.
 *
 * Tabel: pendaftaran_asprak
 * Status: pending | approved | rejected
 */
class PendaftaranAsprak extends Model
{
    protected $table = 'pendaftaran_asprak';

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'ipk',
        'motivasi',
        'cv_path',
        'jadwal',
        'status',
        'alasan_penolakan',
    ];

    protected $casts = [
        'jadwal' => 'array',
        'ipk'    => 'float',
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
