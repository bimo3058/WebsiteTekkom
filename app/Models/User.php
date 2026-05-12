<?php

namespace App\Models;

use App\Services\PermissionAssigner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'external_id',
        'name',
        'email',
        'password',
        'whatsapp',
        'sso_data',
        'last_synced_from_sso',
        'last_login',
        'is_online',
        'suspended_at',
        'suspension_reason',
        'personal_email',
        'avatar_url',
        'avatar_url_format',
    ];

    /**
     * Hidden attributes for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sso_data' => 'json',
            'last_login' => 'datetime',
            'last_synced_from_sso' => 'datetime',
            'suspended_at' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->syncPermissionsFromRoles();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value)
                    return null;

                return cache()->remember(
                    "user_avatar_{$this->id}_" . md5($value),
                    now()->addDay(),
                    function () use ($value) {
                        return str_starts_with($value, 'http')
                            ? $value
                            : asset('storage/' . $value);
                    }
                );
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withTimestamps();
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    public function praktikumAsparaks()
    {
        return $this->belongsToMany(Praktikum::class, 'asprak_praktikum', 'user_id', 'praktikum_id')
            ->withPivot('id', 'role', 'deskripsi', 'deleted_at')
            ->withTimestamps()
            ->whereNull('asprak_praktikum.deleted_at');
    }

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE & PERMISSION LOGIC
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $roleName, ?string $module = null): bool
    {
        return $this->getCachedRoles()
            ->when($module, fn($c) => $c->where('module', $module))
            ->contains('name', strtolower($roleName));
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->getCachedRoles()->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function isAcademic(): bool
    {
        return $this->getCachedRoles()->contains('is_academic', true);
    }

    public function syncPermissionsFromRoles(): void
    {
        $roles = $this->getCachedRoles()->pluck('name')->toArray();

        if (empty($roles)) {
            $this->directPermissions()->detach();
            $this->clearUserCache();
            return;
        }

        PermissionAssigner::assignByRoles($this, $roles);
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->hasRole('superadmin'))
            return true;

        return $this->getAllPermissions()->contains(strtolower(trim($permissionName)));
    }

    public function can($abilities, $arguments = [])
    {
        if (is_string($abilities) && str_contains($abilities, '.')) {
            return $this->hasPermissionTo($abilities);
        }
        return parent::can($abilities, $arguments);
    }

    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        return Cache::remember("user:{$this->id}:all_permissions_final", 3600, function () {
            $roles = $this->roles()->with('permissions')->get();
            $direct = $this->directPermissions()->pluck('name');

            return $roles
                ->flatMap(fn($role) => $role->permissions->pluck('name'))
                ->merge($direct)
                ->map(fn($p) => strtolower(trim($p)))
                ->unique()
                ->values();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CACHING & STATUS
    |--------------------------------------------------------------------------
    */

    public function getCachedRoles(): \Illuminate\Support\Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles;
        }

        $cached = Cache::get("user:{$this->id}:roles");
        if ($cached)
            return collect($cached);

        $roles = $this->roles()->get();

        Cache::put(
            "user:{$this->id}:roles",
            $roles->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'module' => $r->module,
                'is_academic' => (bool) $r->is_academic,
            ])->toArray(),
            now()->addHours(8)
        );

        return $roles;
    }

    public function clearUserCache(): void
    {
        Cache::forget("user:{$this->id}:data");
        Cache::forget("user:{$this->id}:roles");
        Cache::forget("user:{$this->id}:permissions");
        Cache::forget("user:{$this->id}:all_permissions_final");
        Cache::forget("user_permissions_{$this->id}");
    }

    /**
     * Check if the user account is suspended.
     */
    public function isSuspended(): bool
    {
        return !is_null($this->suspended_at);
    }

    /**
     * Cache essential user data for fast access.
     */
    public function cacheUserData(): void
    {
        Cache::put("user:{$this->id}:data", [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ], now()->addHours(8));
    }

    public function recordLogin(): void
    {
        static::where('id', $this->id)->update([
            'last_login' => now(),
            'is_online' => DB::raw('true'),
        ]);
    }
}