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
        'nim',
        'angkatan',
        'program_studi',
        'tahun_lulus',
        'perusahaan',
        'jabatan',
        'bidang_industri',
        'tahun_mulai_bekerja',
        'status_karir',
        'linkedin',
    ];

    protected $casts = [
        'angkatan'            => 'integer',
        'tahun_lulus'         => 'integer',
        'tahun_mulai_bekerja' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_BEKERJA      = 'bekerja';
    const STATUS_WIRAUSAHA    = 'wirausaha';
    const STATUS_STUDI_LANJUT = 'studi_lanjut';
    const STATUS_BELUM_BEKERJA = 'belum_bekerja';

    const STATUS_LIST = [
        self::STATUS_BEKERJA,
        self::STATUS_WIRAUSAHA,
        self::STATUS_STUDI_LANJUT,
        self::STATUS_BELUM_BEKERJA,
    ];

    const STATUS_LABELS = [
        self::STATUS_BEKERJA      => 'Bekerja',
        self::STATUS_WIRAUSAHA    => 'Wirausaha',
        self::STATUS_STUDI_LANJUT => 'Studi Lanjut',
        self::STATUS_BELUM_BEKERJA => 'Belum Terdata',
    ];

    const BIDANG_INDUSTRI_LIST = [
        'teknologi_informasi'    => 'Teknologi Informasi',
        'keuangan_perbankan'     => 'Keuangan & Perbankan',
        'pendidikan'             => 'Pendidikan',
        'kesehatan'              => 'Kesehatan',
        'manufaktur'             => 'Manufaktur',
        'telekomunikasi'         => 'Telekomunikasi',
        'pemerintahan'           => 'Pemerintahan',
        'konsultan'              => 'Konsultan',
        'e_commerce'             => 'E-Commerce',
        'startup'                => 'Startup',
        'lainnya'                => 'Lainnya',
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

    public function scopeByStatusKarir(Builder $query, string $status): Builder
    {
        return $query->where('status_karir', $status);
    }

    public function scopeByBidangIndustri(Builder $query, string $bidang): Builder
    {
        return $query->where('bidang_industri', $bidang);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        if ($status === self::STATUS_BEKERJA) {
            return $query->whereNotNull('perusahaan');
        } elseif ($status === self::STATUS_BELUM_BEKERJA) {
            return $query->whereNull('perusahaan');
        }
        return $query;
    }

    public function scopeSudahBekerja(Builder $query): Builder
    {
        return $query->whereIn('status_karir', [self::STATUS_BEKERJA, self::STATUS_WIRAUSAHA]);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                     ->orWhere('perusahaan', 'like', "%{$keyword}%")
                     ->orWhere('jabatan', 'like', "%{$keyword}%")
                     ->orWhere('nim', 'like', "%{$keyword}%");
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getStatusKarirLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status_karir] ?? ucfirst($this->status_karir ?? 'Belum Terdata');
    }

    public function getBidangIndustriLabelAttribute(): string
    {
        return self::BIDANG_INDUSTRI_LIST[$this->bidang_industri] ?? ucfirst(str_replace('_', ' ', $this->bidang_industri ?? ''));
    }

    /**
     * Hitung waktu tunggu (tahun) dari lulus hingga mulai bekerja.
     */
    public function getWaktuTungguAttribute(): ?int
    {
        if ($this->tahun_lulus && $this->tahun_mulai_bekerja) {
            return max(0, $this->tahun_mulai_bekerja - $this->tahun_lulus);
        }
        return null;
    }
}