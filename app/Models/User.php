<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'email',
        'password',
        'sso_data',
        'last_synced_from_sso',
        'last_login',
    ];

    protected $hidden = [
        'password',
        // remember_token TIDAK di-hidden supaya ikut ter-cache
        // dan tidak trigger strict mode error saat rebuild dari cache array
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'sso_data'             => 'json',
            'last_login'           => 'datetime',
            'last_synced_from_sso' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->select('roles.id', 'roles.name', 'roles.module');
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function auditLogs()
    {
        return $this->hasMany(UserAuditLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $roleName, ?string $module = null): bool
    {
        return $this->getCachedRoles()
            ->when($module, fn($c) => $c->where('module', $module))
            ->contains('name', strtolower($roleName));
    }

    public function hasAnyRole(array|string $roleNames, ?string $module = null): bool
    {
        $roleNames = collect(is_string($roleNames) ? [$roleNames] : $roleNames)
            ->map(fn($r) => strtolower($r));

        return $this->getCachedRoles()
            ->when($module, fn($c) => $c->where('module', $module))
            ->whereIn('name', $roleNames)
            ->isNotEmpty();
    }

    public function hasAllRoles(array|string $roleNames, ?string $module = null): bool
    {
        $roleNames = collect(is_string($roleNames) ? [$roleNames] : $roleNames)
            ->map(fn($r) => strtolower($r));

        $userRoleNames = $this->getCachedRoles()
            ->when($module, fn($c) => $c->where('module', $module))
            ->pluck('name');

        return $roleNames->every(fn($r) => $userRoleNames->contains($r));
    }

    /**
     * Ambil roles dari Redis cache, fallback ke DB kalau miss.
     */
    protected function getCachedRoles(): \Illuminate\Support\Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles;
        }

        $cached = Cache::get("user:{$this->id}:roles");

        if ($cached) {
            return collect($cached);
        }

        $roles = $this->roles()->get();
        Cache::put("user:{$this->id}:roles", $roles->toArray(), now()->addHours(8));

        return $roles;
    }

    /**
     * Cache user data ke Redis setelah login.
     * makeVisible(['remember_token']) supaya semua kolom ikut tersimpan
     * dan tidak ada missing attribute error saat di-rebuild dari cache.
     */
    public function cacheUserData(): void
    {
        Cache::put(
            "user:{$this->id}:data",
            $this->makeVisible(['remember_token'])->withoutRelations()->toArray(),
            now()->addHours(8)
        );
    }

    /**
     * Hapus semua cache user — dipanggil saat logout atau update profil.
     */
    public function clearUserCache(): void
    {
        Cache::forget("user:{$this->id}:data");
        Cache::forget("user:{$this->id}:roles");
        Cache::forget("user:{$this->id}:permissions");
    }

    // -------------------------------------------------------------------------
    // Permission helpers
    // -------------------------------------------------------------------------

    /**
     * Ambil semua permission user:
     * gabungan dari role-nya + direct permission
     */
    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        return cache()->remember("user_permissions_{$this->id}", 300, function () {
            $fromRoles = $this->roles()
                ->with('permissions')
                ->get()
                ->flatMap(fn($role) => $role->permissions->pluck('name'));

            $direct = $this->directPermissions->pluck('name');

            return $fromRoles->merge($direct)->unique();
        });
    }

    public function can($abilities, $arguments = [])
    {
        // Superadmin bypass semua permission
        if ($this->hasRole('superadmin')) return true;

        return $this->getAllPermissions()->contains($abilities);
    }

    public function cannot($abilities, $arguments = [])
    {
        return !$this->can($abilities, $arguments);
    }
    // public function can(string $permission, $arguments = []): bool
    // {
    //     // Superadmin bypass semua permission
    //     if ($this->hasRole('superadmin')) return true;

    //     return $this->getAllPermissions()->contains($permission);
    // }

    // public function cannot(string $permission, $arguments = []): bool
    // {
    //     return !$this->can($permission);
    // }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeSuperadmins($query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'superadmin'));
    }

    public function scopeLecturers($query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'dosen'));
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function recordLogin(): void
    {
        dispatch(function () {
            $this->updateQuietly(['last_login' => now()]);
        })->afterResponse();
    }
}