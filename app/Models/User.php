<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'microsoft_id',
        'role',
        'last_login',
        'last_synced_from_sso',
        'sso_data',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'last_synced_from_sso' => 'datetime',
        'sso_data' => 'array',
        'password' => 'hashed',
    ];

    // Audit log relation
    public function auditLogs()
    {
        return $this->hasMany(UserAuditLog::class);
    }

    // Helper untuk determine apakah user dari SSO
    public function isSSoUser()
    {
        return !is_null($this->microsoft_id);
    }
}
