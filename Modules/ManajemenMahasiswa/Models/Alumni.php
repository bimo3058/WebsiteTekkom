<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'mk_alumni';

    protected $fillable = [
        'user_id',
        'angkatan',
        'tahun_lulus',
        'status_posisi_pekerjaan',
        'profesi',
        'kontak_alumni',
    ];

    protected $casts = [
        'angkatan'    => 'integer',
        'tahun_lulus' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_BEKERJA      = 'bekerja';
    const STATUS_WIRAUSAHA     = 'wirausaha';
    const STATUS_STUDI_LANJUT  = 'studi_lanjut';
    const STATUS_BELUM_BEKERJA = 'belum_bekerja';

    const STATUS_LIST = [
        self::STATUS_BEKERJA,
        self::STATUS_WIRAUSAHA,
        self::STATUS_STUDI_LANJUT,
        self::STATUS_BELUM_BEKERJA,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByAngkatan(Builder $query, int $angkatan): Builder
    {
        return $query->where('angkatan', $angkatan);
    }

    public function scopeByTahunLulus(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun_lulus', $tahun);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status_posisi_pekerjaan', $status);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                     ->orWhere('profesi', 'like', "%{$keyword}%");
    }
}