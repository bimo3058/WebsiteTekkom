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

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'sso_data'             => 'json',
            'last_login'           => 'datetime',
            'last_synced_from_sso' => 'datetime',
            'suspended_at'         => 'datetime',
            'is_online'            => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT METHOD
    |--------------------------------------------------------------------------
    | FIX: Hapus event 'updated' dan 'saved' yang menyebabkan syncPermissionsFromRoles()
    | dipanggil 2-3x setiap kali user disimpan (termasuk saat recordLogin,
    | suspend, updateQuietly, dll yang tidak mengubah roles sama sekali).
    |
    | Sync permissions saat roles berubah sudah ditangani secara eksplisit di:
    | - SuperAdminController::updateRole()
    | - SuperAdminController::updatePermissions()
    |
    | Hanya 'created' yang tetap ada karena user baru memang perlu di-assign
    | permissions awal berdasarkan role-nya.
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

    protected function avatarUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if (!$value) return null;

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

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id')
                    ->withTimestamps();
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

    /**
     * FIX: Gunakan getCachedRoles() agar konsisten dengan hasRole(),
     * bukan $this->roles yang bisa memicu query baru kalau relasi belum di-load.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->getCachedRoles()->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function isAcademic(): bool
    {
        return $this->getCachedRoles()->contains('is_academic', true);
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
        $roles = $this->getCachedRoles()->pluck('name')->toArray();

        if (empty($roles)) {
            $this->directPermissions()->detach();
            $this->clearUserCache();
            return;
        }

        PermissionAssigner::assignByRoles($this, $roles);
    }

    /**
     * FIX: Hapus N+1 query — resolve semua permission names dalam satu query,
     * bukan satu query per nama di dalam loop.
     */
    public function syncPermissions($permissions): array
    {
        $input   = collect($permissions);
        $numeric = $input->filter(fn($p) => is_numeric($p))->map(fn($p) => (int) $p);
        $names   = $input->reject(fn($p) => is_numeric($p));

        $resolved   = Permission::whereIn('name', $names)->pluck('id', 'name');
        $notFound   = $names->diff($resolved->keys());

        if ($notFound->isNotEmpty()) {
            Log::warning("Permissions not found during sync", [
                'user_id'   => $this->id,
                'not_found' => $notFound->values(),
            ]);
        }

        $permissionIds = $numeric->merge($resolved->values())->unique()->values()->toArray();

        $result = $this->directPermissions()->sync($permissionIds);
        $this->clearUserCache();

        return $result;
    }

    /**
     * Alias untuk syncPermissionsFromRoles — dipakai oleh PermissionAssigner.
     */
    public function repairPermissions(): void
    {
        $this->syncPermissionsFromRoles();
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->hasRole('superadmin')) return true;

        return $this->getAllPermissions()->contains(strtolower(trim($permissionName)));
    }

    /**
     * Override can() agar permission dengan format 'module.action'
     * (misal: 'banksoal.view') dicek via hasPermissionTo(), bukan Laravel Gate.
     */
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
            $roles  = $this->roles()->with('permissions')->get();
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
    | CACHING HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Ambil roles dari cache, relasi yang sudah di-load, atau query baru.
     * FIX: Cache menyimpan hanya field yang diperlukan (bukan full toArray())
     * agar contains('is_academic', true) dan where('module', ...) bekerja konsisten.
     */
    public function getCachedRoles(): \Illuminate\Support\Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles;
        }

        $cached = Cache::get("user:{$this->id}:roles");
        if ($cached) return collect($cached);

        $roles = $this->roles()->get();

        Cache::put(
            "user:{$this->id}:roles",
            $roles->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'module'      => $r->module,
                'is_academic' => (bool) $r->is_academic,
            ])->toArray(),
            now()->addHours(8)
        );

        return $roles;
    }

    public function cacheUserData(): void
    {
        Cache::put(
            "user:{$this->id}:data",
            $this->makeVisible(['remember_token'])->withoutRelations()->toArray(),
            now()->addHours(8)
        );
    }

    public function clearUserCache(): void
    {
        Cache::forget("user:{$this->id}:data");
        Cache::forget("user:{$this->id}:roles");
        Cache::forget("user:{$this->id}:permissions");
        Cache::forget("user:{$this->id}:all_permissions_final");
        Cache::forget("user_permissions_{$this->id}");
    }

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT STATUS & ACTIONS
    |--------------------------------------------------------------------------
    */

    public function isSuspended(): bool
    {
        return !is_null($this->suspended_at);
    }

    public function suspend(string $reason = ''): void
    {
        $this->update([
            'suspended_at'      => now(),
            'suspension_reason' => $reason,
        ]);
        $this->forceLogout();
    }

    public function unsuspend(): void
    {
        $this->update([
            'suspended_at'      => null,
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

    /**
     * FIX: Gabungkan dua query terpisah menjadi satu round-trip ke database.
     */
    public function recordLogin(): void
    {
        static::where('id', $this->id)->update([
            'last_login' => now(),
            'is_online'  => DB::raw('true'),
        ]);
    }
}