<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class KpDosen extends Model
{
    protected $table = 'eo_kp_dosen';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'kuota_maksimal',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bimbingan()
    {
        return $this->hasMany(KerjaPraktik::class, 'dosen_pembimbing_id');
    }
}
