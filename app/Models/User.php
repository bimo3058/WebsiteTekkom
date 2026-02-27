<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'name',
        'email',
        'password',
        'sso_data',
        'last_synced_from_sso',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'sso_data' => 'json',
            'last_login' => 'datetime',
            'last_synced_from_sso' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all roles for this user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        // Lowercase untuk consistency
        return $this->roles()->where('name', strtolower($roleName))->exists();
    }

    public function hasAnyRole($roleNames)
    {
        if (is_string($roleNames)) {
            return $this->hasRole($roleNames);
        }

        foreach ($roleNames as $roleName) {
            if ($this->hasRole(strtolower($roleName))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles($roleNames)
    {
        if (is_string($roleNames)) {
            return $this->hasRole($roleNames);
        }

        foreach ($roleNames as $roleName) {
            if (!$this->hasRole($roleName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get associated student record if exists
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get associated lecturer record if exists
     */
    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    /**
     * Get user audit logs
     */
    public function auditLogs()
    {
        return $this->hasMany(UserAuditLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get superadmin users
     */
    public function scopeSuperadmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'SUPERADMIN');
        });
    }

    /**
     * Scope to get lecturer users
     */
    public function scopeLecturers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'DOSEN');
        });
    }

    /**
     * Scope to get student users
     */
    public function scopeStudents($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'MAHASISWA');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Update last login timestamp
     */
    public function recordLogin()
    {
        $this->update(['last_login' => now()]);
    }
}