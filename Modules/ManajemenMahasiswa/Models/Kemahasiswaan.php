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
        'ipk',
        'tahun_lulus',
        'profesi',
        'kontak',
    ];

    protected $casts = [
        'angkatan'    => 'integer',
        'tahun_lulus' => 'integer',
        'ipk'         => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_AKTIF        = 'aktif';
    const STATUS_ALUMNI       = 'alumni';
    const STATUS_CUTI         = 'cuti';
    const STATUS_DO           = 'drop_out';
    const STATUS_PINDAH_STUDI = 'pindah_studi';
    const STATUS_WAFAT        = 'wafat';
    const STATUS_MANGKIR      = 'mangkir';

    const STATUS_LIST = [
        self::STATUS_AKTIF,
        self::STATUS_ALUMNI,
        self::STATUS_CUTI,
        self::STATUS_DO,
        self::STATUS_PINDAH_STUDI,
        self::STATUS_WAFAT,
        self::STATUS_MANGKIR,
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

    public function scopeWafat(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_WAFAT);
    }

    public function scopeMangkir(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MANGKIR);
    }

    public function scopeByAngkatan(Builder $query, int $angkatan): Builder
    {
        return $query->where('angkatan', $angkatan);
    }

    /**
     * Pencarian nama / NIM yang mengabaikan besar-kecil huruf.
     * PostgreSQL (Supabase) memperlakukan LIKE sebagai case-sensitive, sehingga
     * "budi" tidak akan menemukan "Budi". Pakai ILIKE di pgsql, LIKE di driver lain.
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $query;
        }

        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        // Escape wildcard agar "100%" tidak dibaca sebagai pola "cocokkan apa saja"
        $needle = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $keyword) . '%';

        return $query->where(function ($q) use ($needle, $operator) {
            $q->where('nama', $operator, $needle)
              ->orWhere('nim', $operator, $needle);
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