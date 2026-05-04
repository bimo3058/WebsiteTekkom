<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SesiLogin extends Model
{
    use HasUuids;

    protected $table = 'sesi_login';

    public $timestamps = false; // Kita pakai custom timestamps

    protected $fillable = [
        'pengguna_id',
        'role_aktif_id',
        'token',
        'login_pada',
        'logout_pada',
        'kedaluwarsa_pada',
    ];

    protected function casts(): array
    {
        return [
            'login_pada'        => 'datetime',
            'logout_pada'       => 'datetime',
            'kedaluwarsa_pada'  => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function roleAktif()
    {
        return $this->belongsTo(Role::class, 'role_aktif_id');
    }
}
