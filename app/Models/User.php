<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->select('roles.id', 'roles.name', 'roles.module', 'roles.is_academic');
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
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION & ROLE LOGIC
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah user memiliki permission tertentu.
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->hasRole('superadmin')) return true;

        $allPermissions = $this->getAllPermissions();
        return $allPermissions->contains(strtolower(trim($permissionName)));
    }

    /**
     * Override method can() bawaan Laravel agar sinkron dengan sistem kita.
     */
    public function can($abilities, $arguments = [])
    {
        if (is_string($abilities) && str_contains($abilities, '.')) {
            return $this->hasPermissionTo($abilities);
        }
        return parent::can($abilities, $arguments);
    }

    /**
     * Ambil semua permission user (Gabungan Role + Direct).
     *
     * ══════════════════════════════════════════════════════════════
     * FIX KRITIS: HAPUS blok hardcoded academic permissions!
     *
     * SEBELUM (BUG):
     *   if ($roles->contains('is_academic', true)) {
     *       $academicPerms = ['banksoal.view', 'banksoal.edit', ...];
     *       $all = $all->merge($academicPerms);
     *   }
     *
     * Blok ini meng-OVERRIDE apa yang disimpan di DB.
     * Meskipun superadmin sudah cabut banksoal.view dari user dosen,
     * kode ini menambahkannya kembali secara paksa → user tetap bisa masuk.
     *
     * SESUDAH (FIX):
     *   Permission HANYA dari 2 sumber:
     *   1. role_permissions (permission yang melekat di role)
     *   2. user_permissions (direct permission per user)
     *   Tidak ada lagi override otomatis berdasarkan is_academic.
     *
     * CATATAN: Jika ingin role akademik AWAL mendapat default
     * permissions, itu dilakukan saat ASSIGN ROLE di SuperAdmin panel
     * (sudah ada autopilot di _scripts.blade.php),
     * bukan di runtime saat cek akses.
     * ══════════════════════════════════════════════════════════════
     */
    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        return Cache::remember("user:{$this->id}:all_permissions_final", 3600, function () {
            $roles = $this->roles()->with('permissions')->get();

            // 1. Permission dari Role (role_permissions table)
            $fromRoles = $roles->flatMap(fn($role) => $role->permissions->pluck('name'));

            // 2. Direct Permission (user_permissions table)
            $direct = $this->directPermissions()->pluck('name');

            // 3. Gabungkan keduanya — TANPA hardcoded override
            $all = $fromRoles->merge($direct);

            return $all->map(fn($p) => strtolower(trim($p)))->unique()->values();
        });
    }

    /**
     * Helper Role dengan support Case-Insensitive
     */
    public function hasRole(string $roleName, ?string $module = null): bool
    {
        return $this->getCachedRoles()
            ->when($module, fn($c) => $c->where('module', $module))
            ->contains('name', strtolower($roleName));
    }

    /**
     * Cek apakah user memiliki Role Akademik (Dosen/Mhs/GPM)
     */
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

    /**
     * FIX: Hapus SEMUA cache key terkait permission.
     * Key 'all_permissions_final' sebelumnya TIDAK dihapus,
     * sehingga perubahan permission dari SuperAdmin panel
     * tidak berlaku sampai cache expire (1 jam).
     */
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
        DB::table('sessions')->where('user_id', $this->id)->delete();
        $this->clearUserCache();
    }

    public function recordLogin(): void
    {
        dispatch(function () {
            $this->updateQuietly(['last_login' => now()]);
        })->afterResponse();
    }
}