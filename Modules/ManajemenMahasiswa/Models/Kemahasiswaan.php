<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Kemahasiswaan extends Model
{
    use HasFactory;

    protected $table = 'mk_kemahasiswaan';

    protected $fillable = [
        'user_id',
        'nama',
        'nim',
        'angkatan',
        'status',
        'tahun_lulus',
        'profesi',
        'kontak',
    ];

    protected $casts = [
        'angkatan'    => 'integer',
        'tahun_lulus' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_AKTIF   = 'aktif';
    const STATUS_ALUMNI  = 'alumni';
    const STATUS_CUTI    = 'cuti';
    const STATUS_DO      = 'drop_out';

    const STATUS_LIST = [
        self::STATUS_AKTIF,
        self::STATUS_ALUMNI,
        self::STATUS_CUTI,
        self::STATUS_DO,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'kemahasiswaan_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeAlumni(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ALUMNI);
    }

    public function scopeByAngkatan(Builder $query, int $angkatan): Builder
    {
        return $query->where('angkatan', $angkatan);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('nim', 'like', "%{$keyword}%");
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }
}