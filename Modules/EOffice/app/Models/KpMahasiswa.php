<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class KpMahasiswa extends Model
{
    protected $table = 'eo_kp_mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_lengkap',
        'prodi',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kerjaPraktik()
    {
        return $this->hasMany(KerjaPraktik::class, 'mahasiswa_id');
    }
}
