<?php

namespace App\Models;

use App\Services\PermissionAssigner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Throwable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    public const AUTH_CACHE_HAS_REMEMBER_TOKEN = '__auth_has_remember_token';

    public const AUTH_CACHE_REMEMBER_TOKEN_PLACEHOLDER = '__cached_remember_token__';

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

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'sso_data' => 'json',
            'last_login' => 'datetime',
            'last_synced_from_sso' => 'datetime',
            'suspended_at' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT METHOD
    |--------------------------------------------------------------------------
    | Sync permissions saat user baru dibuat. Update roles ditangani secara
    | eksplisit di SuperAdminController::updateRole() agar tidak redundant.
    |--------------------------------------------------------------------------
    */

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
                if (! $value) {
                    return null;
                }

                return cache()->remember(
                    "user_avatar_{$this->id}_".md5($value),
                    now()->addDay(),
                    function () use ($value) {
                        return str_starts_with($value, 'http')
                            ? $value
                            : asset('storage/'.$value);
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

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    | hasRole(), hasAnyRole(), hasAllRoles() sudah disediakan oleh HasRoles trait.
    | isAcademic() adalah custom logic yang tidak ada di Spatie.
    |--------------------------------------------------------------------------
    */

    public function isAcademic(): bool
    {
        return $this->roles->contains('is_academic', true);
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION LOGIC
    |--------------------------------------------------------------------------
    */

    /**
     * Sync permissions berdasarkan roles yang dimiliki user.
     * Dipanggil manual dari controller setelah roles diubah,
     * atau otomatis via boot::created untuk user baru.
     */
    public function syncPermissionsFromRoles(): void
    {
        $roleNames = $this->roles()->pluck('name')->toArray();

        if (empty($roleNames)) {
            $this->permissions()->detach();
            $this->clearUserCache();

            return;
        }

        PermissionAssigner::assignByRoles($this, $roleNames);
    }

    /**
     * Alias untuk syncPermissionsFromRoles — dipakai oleh PermissionAssigner.
     */
    public function repairPermissions(): void
    {
        $this->syncPermissionsFromRoles();
    }

    /*
    |--------------------------------------------------------------------------
    | CACHING HELPERS
    |--------------------------------------------------------------------------
    */

    public function cacheUserData(): void
    {
        try {
            Cache::put(
                "user:{$this->id}:data",
                $this->authCachePayload(),
                max(1, (int) config('auth.cache.user_ttl_seconds', 28_800))
            );
        } catch (Throwable) {
            // The database remains the source of truth when Redis is unavailable.
        }
    }

    /**
     * Cache auth-safe attributes without persisting password or remember token.
     * The boolean flag lets the session guard rotate an existing token on
     * logout after the model is hydrated from cache.
     *
     * @return array<string, mixed>
     */
    public function authCachePayload(): array
    {
        $attributes = collect($this->getAttributes())
            ->except(['password', 'remember_token'])
            ->all();

        $attributes[self::AUTH_CACHE_HAS_REMEMBER_TOKEN] =
            ! empty($this->getRawOriginal($this->getRememberTokenName()));

        return $attributes;
    }

    /**
     * @return Collection<int, array{id:mixed, name:string, module:mixed, is_academic:bool}>
     */
    public function getCachedRoleData(): Collection
    {
        $cacheKey = "user:{$this->id}:roles";

        try {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return collect($cached);
            }
        } catch (Throwable) {
            // Fall through to the database.
        }

        $roles = $this->roles()->get()->map(fn ($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'module' => $role->module,
            'is_academic' => (bool) $role->is_academic,
        ])->values();

        try {
            Cache::put(
                $cacheKey,
                $roles->all(),
                max(1, (int) config('auth.cache.user_ttl_seconds', 28_800))
            );
        } catch (Throwable) {
            // The role query result remains valid for this request.
        }

        return $roles;
    }

    public function clearUserCache(): void
    {
        try {
            Cache::forget("user:{$this->id}:data");
            Cache::forget("user:{$this->id}:roles");
            Cache::forget("user:{$this->id}:permissions");
            Cache::forget("user:{$this->id}:all_permissions_final");
            Cache::forget("user_permissions_{$this->id}");
            Cache::forget("user:{$this->id}:sso-academic-synced");
        } catch (Throwable) {
            // Cache invalidation must not block database updates.
        }

        try {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (Throwable) {
            // Spatie will rebuild its cache on the next healthy request.
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT STATUS & ACTIONS
    |--------------------------------------------------------------------------
    */

    public function isSuspended(): bool
    {
        return ! is_null($this->getAttributes()['suspended_at'] ?? null);
    }

    public function suspend(string $reason = ''): void
    {
        $this->update([
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
        $this->forceLogout();
    }

    public function unsuspend(): void
    {
        $this->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
    }

    public function forceLogout(): void
    {
        static::where('id', $this->id)->update([
            'is_online' => DB::raw('false'),
        ]);
        $this->increment('session_version');
        $this->clearUserCache();
    }

    public function recordLogin(): void
    {
        $loggedInAt = now();

        static::where('id', $this->id)->update([
            'last_login' => $loggedInAt,
            'is_online' => DB::raw('true'),
        ]);

        $this->forceFill([
            'last_login' => $loggedInAt,
            'is_online' => true,
        ]);
    }
}
