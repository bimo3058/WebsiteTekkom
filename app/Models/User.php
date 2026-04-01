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
        'suspended_at',
        'suspension_reason',
        'personal_email',
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
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT METHOD - AUTO SYNC PERMISSIONS
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        // AFTER USER CREATED
        static::created(function ($user) {
            Log::info("User created event triggered", [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            // Sync permissions based on roles
            $user->syncPermissionsFromRoles();
        });

        // AFTER USER UPDATED
        static::updated(function ($user) {
            Log::info("User updated event triggered", [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            // Sync permissions based on roles
            $user->syncPermissionsFromRoles();
        });

        // AFTER ROLES ARE ATTACHED
        static::saved(function ($user) {
            // Check if roles relationship was modified
            if ($user->isDirty() || $user->roles()->getQuery()->exists()) {
                Log::info("User saved with potential role changes", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                $user->syncPermissionsFromRoles();
            }
        });
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

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
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
    | PERMISSION & ROLE LOGIC
    |--------------------------------------------------------------------------
    */

    /**
     * SYNC PERMISSIONS FROM ROLES - THIS IS THE MAIN METHOD
     */
    public function syncPermissionsFromRoles(): void
    {
        // $roles = $this->roles()->pluck('name')->toArray();
        $roles = $this->getCachedRoles()->pluck('name')->toArray();
        
        Log::info("Syncing permissions from roles", [
            'user_id' => $this->id,
            // 'email' => $this->email,
            'roles' => $roles
        ]);
        
        // if (empty($roles)) {
        //     Log::warning("No roles found for user, clearing permissions", [
        //         'user_id' => $this->id
        //     ]);
        //     $this->directPermissions()->detach();
        //     $this->clearUserCache();
        //     return;
        // }
        if (empty($roles)) {
            $this->directPermissions()->detach();
            $this->clearUserCache();
            return;
        }
        
        PermissionAssigner::assignByRoles($this, $roles);
    }

    /**
     * Sync permissions manually (wrapper)
     */
    public function syncPermissions($permissions)
    {
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            if (is_numeric($permission)) {
                $permissionIds[] = $permission;
            } else {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $permissionIds[] = $perm->id;
                } else {
                    Log::warning("Permission not found for sync", [
                        'permission' => $permission,
                        'user_id' => $this->id
                    ]);
                }
            }
        }
        
        $permissionIds = array_unique($permissionIds);
        
        Log::info("Syncing specific permissions", [
            'user_id' => $this->id,
            'permission_ids' => $permissionIds
        ]);
        
        $result = $this->directPermissions()->sync($permissionIds);
        $this->clearUserCache();
        
        return $result;
    }

    /**
     * Repair permissions if they don't match roles
     */
    public function repairPermissions(): void
    {
        $this->syncPermissionsFromRoles();
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->hasRole('superadmin')) return true;

        $allPermissions = $this->getAllPermissions();
        return $allPermissions->contains(strtolower(trim($permissionName)));
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

            $fromRoles = $roles->flatMap(fn($role) => $role->permissions->pluck('name'));
            $direct = $this->directPermissions()->pluck('name');
            $all = $fromRoles->merge($direct);

            return $all->map(fn($p) => strtolower(trim($p)))->unique()->values();
        });
    }

    public function assignPermissionsFromRoles(): void
    {
        $roleNames = $this->roles()->pluck('name')->toArray();
        
        if (empty($roleNames)) {
            \Log::warning("No roles found for user", ['user_id' => $this->id]);
            return;
        }
        
        $permissionIds = [];
        
        $roleModuleMap = [
            'admin_banksoal' => ['banksoal'],
            'admin_capstone' => ['capstone'],
            'admin_eoffice' => ['eoffice'],
            'admin_kemahasiswaan' => ['kemahasiswaan'],
            'dosen' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
            'mahasiswa' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
            'gpm' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
            'superadmin' => ['all'],
        ];
        
        $actions = ['view', 'edit', 'delete'];
        
        foreach ($roleNames as $roleName) {
            $modules = $roleModuleMap[$roleName] ?? [];
            
            if (in_array('all', $modules)) {
                $permissionIds = Permission::all()->pluck('id')->toArray();
                break;
            }
            
            foreach ($modules as $module) {
                foreach ($actions as $action) {
                    $permissionName = "{$module}.{$action}";
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission) {
                        $permissionIds[] = $permission->id;
                    }
                }
            }
        }
        
        $permissionIds = array_unique($permissionIds);
        $this->directPermissions()->sync($permissionIds);
        
        \Log::info("Permissions assigned from roles", [
            'user_id' => $this->id,
            'email' => $this->email,
            'roles' => $roleNames,
            'permission_count' => count($permissionIds)
        ]);
    }

    public function hasRole(string $roleName, ?string $module = null): bool
    {
        return $this->getCachedRoles()
            ->when($module, fn($c) => $c->where('module', $module))
            ->contains('name', strtolower($roleName));
    }

    public function isAcademic(): bool
    {
        return $this->getCachedRoles()->contains('is_academic', true);
    }

    /*
    |--------------------------------------------------------------------------
    | CACHING HELPERS
    |--------------------------------------------------------------------------
    */

    protected function getCachedRoles(): \Illuminate\Support\Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles;
        }

        $cached = Cache::get("user:{$this->id}:roles");
        if ($cached) return collect($cached);

        $roles = $this->roles()->get();
        Cache::put("user:{$this->id}:roles", $roles->toArray(), now()->addHours(8));

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
            'suspended_at'       => now(),
            'suspension_reason'  => $reason,
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
        $this->increment('session_version');
        $this->clearUserCache();
    }

    public function recordLogin(): void
    {
        dispatch(function () {
            $this->updateQuietly(['last_login' => now()]);
        })->afterResponse();
    }
}