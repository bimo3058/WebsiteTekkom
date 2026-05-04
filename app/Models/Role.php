<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
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
=======

class Role extends Model
{
    // Tambahkan is_academic ke fillable
    protected $fillable = [
        'name', 
        'module', 
        'is_academic'
    ];

    // Tambahkan casting agar dibaca sebagai boolean
    protected $casts = [
        'is_academic' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f
