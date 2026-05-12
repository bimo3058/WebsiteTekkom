<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemRole extends Model
{
    use HasUuids;

    protected $table = 'role';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function penggunas()
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_role', 'role_id', 'pengguna_id')
                    ->withPivot('status', 'dibuat_pada')
                    ->withTimestamps();
    }
}
