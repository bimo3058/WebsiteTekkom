<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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