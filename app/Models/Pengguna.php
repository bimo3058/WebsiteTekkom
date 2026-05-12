<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable, SoftDeletes, LogsActivity;

    protected $table = 'pengguna';

    // Activity log fields to track
    protected static array $logAttributes = ['nama', 'email', 'nim_nip', 'status'];

    protected $fillable = [
        'nama',
        'email',
        'password_hash',
        'nim_nip',
        'status',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Sanctum uses 'password' column by default — override to use password_hash.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'deleted_at'    => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Semua role yang dimiliki pengguna ini (via pivot).
     */
    public function roles()
    {
        return $this->belongsToMany(SystemRole::class, 'pengguna_role', 'pengguna_id', 'role_id')
                    ->withPivot('status', 'dibuat_pada')
                    ->withTimestamps();
    }

    /**
     * Semua sesi login pengguna ini.
     */
    public function sesiLogin()
    {
        return $this->hasMany(SesiLogin::class, 'pengguna_id');
    }

    /**
     * Sesi login terakhir yang aktif.
     */
    public function sesiAktif()
    {
        return $this->hasOne(SesiLogin::class, 'pengguna_id')
                    ->latest('login_pada')
                    ->whereNull('logout_pada');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Cek apakah pengguna memiliki role tertentu (berdasarkan nama role).
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()
                    ->where('nama', $roleName)
                    ->where('pengguna_role.status', 'aktif')
                    ->exists();
    }

    /**
     * Cek apakah pengguna memiliki salah satu dari role yang diberikan.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()
                    ->whereIn('nama', $roles)
                    ->where('pengguna_role.status', 'aktif')
                    ->exists();
    }

    /**
     * Role hierarchy untuk backend logic (bukan di DB).
     * Makin tinggi = makin besar nilai.
     */
    public static function roleHierarchy(): array
    {
        return [
            'mahasiswa'  => 1,
            'asprak'     => 2,
            'koor_prak'  => 3,
            'dosen'      => 4,
            'admin'      => 5,
        ];
    }

    /**
     * Ambil role tertinggi yang dimiliki pengguna ini.
     */
    public function roleTertinggi(): ?string
    {
        $hierarchy  = self::roleHierarchy();
        $roleNames  = $this->roles()
                           ->where('pengguna_role.status', 'aktif')
                           ->pluck('nama')
                           ->toArray();

        if (empty($roleNames)) {
            return null;
        }

        usort($roleNames, fn($a, $b) => ($hierarchy[$b] ?? 0) - ($hierarchy[$a] ?? 0));

        return $roleNames[0];
    }
}
