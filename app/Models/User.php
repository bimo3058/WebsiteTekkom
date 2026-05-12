<?php

namespace App\Models;

use App\Services\PermissionAssigner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens, HasRoles;

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

    protected function avatarUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
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
        Cache::put(
            "user:{$this->id}:data",
            $this->getAttributes(),
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

        // Bersihkan cache Spatie sekaligus
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
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
        static::where('id', $this->id)->update([
            'last_login' => now(),
            'is_online' => DB::raw('true'),
        ]);
    }
}
