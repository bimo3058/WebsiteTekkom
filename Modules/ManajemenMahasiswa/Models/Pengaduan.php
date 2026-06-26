<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'mk_pengaduan';

    protected $fillable = [
        'user_id',
        'kategori',
        'is_anonim',
        'anon_token',
        'data_template',
        'status',
        'read_at',
        'read_by',
        'jawaban',
        'answered_at',
        'answered_by',
        'reopen_count',
        'reopen_reason',
        'auto_close_at',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'is_anonim'      => 'bool',
        'data_template'  => 'array',
        'read_at'        => 'datetime',
        'answered_at'    => 'datetime',
        'auto_close_at'  => 'datetime',
        'closed_at'      => 'datetime',
    ];

    public const STATUS_DRAFT             = 'draft';
    public const STATUS_BARU              = 'baru';
    public const STATUS_DIBACA            = 'dibaca';
    public const STATUS_DIDELEGASIKAN     = 'didelegasikan';
    public const STATUS_SELESAI           = 'selesai';

    public const MAX_REOPEN = 2;

    // Kategori baru (utama)
    public const KATEGORI_AKADEMIK_ADMINISTRASI = 'akademik_administrasi';
    public const KATEGORI_PROSES_PEMBELAJARAN = 'proses_pembelajaran';
    public const KATEGORI_FASILITAS_KAMPUS = 'fasilitas_kampus';
    public const KATEGORI_LAYANAN_IT_SSO = 'layanan_it_sso';
    public const KATEGORI_KEGIATAN_KEMAHASISWAAN = 'kegiatan_kemahasiswaan';
    public const KATEGORI_KEAMANAN_KETERTIBAN = 'keamanan_ketertiban';
    public const KATEGORI_KESEHATAN_KONSELING = 'kesehatan_konseling';
    public const KATEGORI_TINDAKAN_TIDAK_MENYENANGKAN = 'tindakan_tidak_menyenangkan';

    // Legacy (masih diterima untuk data lama)
    public const KATEGORI_AKADEMIK = 'akademik';
    public const KATEGORI_PEMBELAJARAN = 'pembelajaran';
    public const KATEGORI_TENDIK = 'tendik';
    public const KATEGORI_TUGAS_BEBAN = 'tugas_beban';
    public const KATEGORI_LAINNYA = 'lainnya';

    public const KATEGORI_LIST = [
        // Kategori utama (8)
        self::KATEGORI_AKADEMIK_ADMINISTRASI,
        self::KATEGORI_PROSES_PEMBELAJARAN,
        self::KATEGORI_FASILITAS_KAMPUS,
        self::KATEGORI_LAYANAN_IT_SSO,
        self::KATEGORI_KEGIATAN_KEMAHASISWAAN,
        self::KATEGORI_KEAMANAN_KETERTIBAN,
        self::KATEGORI_KESEHATAN_KONSELING,
        self::KATEGORI_TINDAKAN_TIDAK_MENYENANGKAN,
    ];

    public const LEGACY_KATEGORI_MAP = [
        self::KATEGORI_AKADEMIK => self::KATEGORI_AKADEMIK_ADMINISTRASI,
        self::KATEGORI_TENDIK => self::KATEGORI_AKADEMIK_ADMINISTRASI,
        self::KATEGORI_PEMBELAJARAN => self::KATEGORI_PROSES_PEMBELAJARAN,
        self::KATEGORI_TUGAS_BEBAN => self::KATEGORI_PROSES_PEMBELAJARAN,
        // Legacy yang terlalu umum dipetakan ke kategori paling umum agar tag legacy tidak muncul lagi
        self::KATEGORI_LAINNYA => self::KATEGORI_AKADEMIK_ADMINISTRASI,
    ];

    public static function normalizeKategori(string $kategori): string
    {
        return self::LEGACY_KATEGORI_MAP[$kategori] ?? $kategori;
    }

    /**
     * Return daftar kategori legacy yang dipetakan ke kategori utama tertentu.
     */
    public static function legacyKeysFor(string $kategoriUtama): array
    {
        $keys = [];
        foreach (self::LEGACY_KATEGORI_MAP as $legacy => $mapped) {
            if ($mapped === $kategoriUtama) {
                $keys[] = $legacy;
            }
        }

        return $keys;
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function dibacaOleh(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'read_by');
    }


    public function ditutupOleh(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }

    public function delegasi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PengaduanDelegasi::class, 'pengaduan_id')->orderByDesc('delegated_at');
    }

    public function delegasiAktif(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PengaduanDelegasi::class, 'pengaduan_id')
            ->where('status', 'aktif')
            ->latestOfMany('delegated_at');
    }

    /**
     * Delegasi terakhir (apapun statusnya) — digunakan untuk menampilkan
     * tanggapan dosen bahkan setelah delegasi di-forward ke mahasiswa.
     */
    public function delegasiTerakhir(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PengaduanDelegasi::class, 'pengaduan_id')
            ->latestOfMany('delegated_at');
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PengaduanLog::class, 'pengaduan_id')->orderByDesc('created_at');
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }
}
