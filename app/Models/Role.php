<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'module',
        'is_academic',
    ];

    protected $casts = [
        'is_academic' => 'boolean',
    ];
}
