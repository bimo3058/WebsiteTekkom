<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;

    protected $table = 'role';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Semua pengguna yang memiliki role ini.
     */
    public function pengguna()
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_role', 'role_id', 'pengguna_id')
                    ->withPivot('status', 'dibuat_pada');
    }

    /**
     * Semua sesi yang menggunakan role ini sebagai role aktif.
     */
    public function sesiLogin()
    {
        return $this->hasMany(SesiLogin::class, 'role_aktif_id');
    }
}
