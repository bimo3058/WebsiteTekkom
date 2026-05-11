<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'mk_kegiatan';

    protected $fillable = [
        'user_id',
        'kategori_kegiatan_id',
        'bidang_id',
        'tahun',
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'jam_mulai',
        'tanggal_selesai',
        'jam_selesai',
        'lokasi',
        'banner',
        'surat_proker',
        'surat_proker_original',
        'anggaran',
        'penanggung_jawab',
        'ketua_pelaksana_id',
        'dosen_pendamping_id',
        'target_peserta',
        'status',
        // Kolom realisasi (Subbab 2 — disimpan terpisah dari rencana)
        'realisasi_tanggal_mulai',
        'realisasi_tanggal_selesai',
        'realisasi_lokasi',
        'realisasi_peserta',
        'realisasi_anggaran',
        'catatan_pelaksanaan',
        'catatan_penolakan',
        'disetujui_oleh',
        'disetujui_at',
    ];

    protected $casts = [
        'tanggal_mulai'             => 'date',
        'tanggal_selesai'           => 'date',
        'realisasi_tanggal_mulai'   => 'date',
        'realisasi_tanggal_selesai' => 'date',
        'anggaran'                  => 'decimal:2',
        'realisasi_anggaran'        => 'decimal:2',
        'disetujui_at'              => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Attribute Accessors for Time
    // -------------------------------------------------------------------------

    public function getJamMulaiFormattedAttribute(): ?string
    {
        return $this->jam_mulai ? \Carbon\Carbon::parse($this->jam_mulai)->format('H:i') : null;
    }

    public function getJamSelesaiFormattedAttribute(): ?string
    {
        return $this->jam_selesai ? \Carbon\Carbon::parse($this->jam_selesai)->format('H:i') : null;
    }

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    // ── Status Perencanaan (Subbab 1) ──────────────────────────────────────
    const STATUS_DRAFT             = 'draft';              // baru dibuat, belum diajukan
    const STATUS_DIAJUKAN          = 'diajukan';           // (legacy) sudah diajukan
    const STATUS_TTD_KETUA         = 'menunggu_ttd_ketua'; // menunggu TTD ketua himpunan + bendahara
    const STATUS_TTD_DPM           = 'menunggu_ttd_dpm';   // menunggu TTD DPM
    const STATUS_TTD_DEPT          = 'menunggu_ttd_dept';  // menunggu TTD ketua departemen
    const STATUS_DISETUJUI         = 'disetujui';          // semua TTD selesai, siap dilaksanakan
    const STATUS_DITOLAK           = 'ditolak';            // ditolak, perlu revisi

    // ── Status Pelaksanaan (Subbab 2 & 3) ────────────────────────────────
    const STATUS_AKAN_DATANG = 'akan_datang'; // proker disetujui, belum terlaksana
    const STATUS_BERLANGSUNG = 'berlangsung';
    const STATUS_SELESAI     = 'selesai';

    const STATUS_LIST = [
        self::STATUS_DRAFT,
        self::STATUS_DIAJUKAN,
        self::STATUS_TTD_KETUA,
        self::STATUS_TTD_DPM,
        self::STATUS_TTD_DEPT,
        self::STATUS_DISETUJUI,
        self::STATUS_DITOLAK,
        self::STATUS_AKAN_DATANG,
        self::STATUS_BERLANGSUNG,
        self::STATUS_SELESAI,
    ];

    /** Status yang masih dalam fase perencanaan / penandatanganan (Subbab 1) */
    const STATUS_PERENCANAAN = [
        self::STATUS_DRAFT,
        self::STATUS_DIAJUKAN,
        self::STATUS_TTD_KETUA,
        self::STATUS_TTD_DPM,
        self::STATUS_TTD_DEPT,
        self::STATUS_DITOLAK,
    ];

    /** Status yang sudah disetujui / dalam proses pelaksanaan (Subbab 2) */
    const STATUS_PELAKSANAAN = [
        self::STATUS_DISETUJUI,
        self::STATUS_AKAN_DATANG,
        self::STATUS_BERLANGSUNG,
    ];

    /** Label status untuk ditampilkan di UI */
    const STATUS_LABELS = [
        self::STATUS_DRAFT          => 'Draft',
        self::STATUS_DIAJUKAN       => 'Diajukan',
        self::STATUS_TTD_KETUA      => 'TTD Ketua & Bendahara',
        self::STATUS_TTD_DPM        => 'TTD DPM',
        self::STATUS_TTD_DEPT       => 'TTD Ketua Departemen',
        self::STATUS_DISETUJUI      => 'Disetujui',
        self::STATUS_DITOLAK        => 'Ditolak',
        self::STATUS_AKAN_DATANG    => 'Akan Datang',
        self::STATUS_BERLANGSUNG    => 'Berlangsung',
        self::STATUS_SELESAI        => 'Selesai',
    ];

    /** Warna badge per status */
    const STATUS_COLORS = [
        self::STATUS_DRAFT          => 'secondary',
        self::STATUS_DIAJUKAN       => 'warning',
        self::STATUS_TTD_KETUA      => 'warning',
        self::STATUS_TTD_DPM        => 'info',
        self::STATUS_TTD_DEPT       => 'primary',
        self::STATUS_DISETUJUI      => 'success',
        self::STATUS_DITOLAK        => 'danger',
        self::STATUS_AKAN_DATANG    => 'info',
        self::STATUS_BERLANGSUNG    => 'primary',
        self::STATUS_SELESAI        => 'success',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kategoriKegiatan(): BelongsTo
    {
        return $this->belongsTo(KategoriKegiatan::class, 'kategori_kegiatan_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    /**
     * Many-to-many: Kegiatan can have multiple Kategori (max 2).
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(KategoriKegiatan::class, 'mk_kegiatan_kategori', 'kegiatan_id', 'kategori_kegiatan_id')->withTimestamps();
    }

    /**
     * Many-to-many: Kegiatan can have multiple Bidang.
     */
    public function bidangs(): BelongsToMany
    {
        return $this->belongsToMany(Bidang::class, 'mk_kegiatan_bidang', 'kegiatan_id', 'bidang_id')->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function ketuaPelaksana(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'ketua_pelaksana_id');
    }

    public function dosenPendamping(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lecturer::class, 'dosen_pendamping_id');
    }

    /**
     * Many-to-many: Kegiatan dapat memiliki banyak panitia (mahasiswa).
     */
    public function panitia(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Student::class, 'mk_kegiatan_panitia', 'kegiatan_id', 'student_id')
                    ->withPivot('peran')
                    ->withTimestamps();
    }

    public function riwayatKegiatan(): HasMany
    {
        return $this->hasMany(RiwayatKegiatan::class, 'kegiatan_id');
    }

    public function disetujuiOleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'disetujui_oleh');
    }

    public function repoMulmed(): HasMany
    {
        return $this->hasMany(RepoMulmed::class, 'kegiatan_id');
    }

    /**
     * Tanda tangan digital yang terpasang pada proker ini.
     */
    public function prokerTtd(): HasMany
    {
        return $this->hasMany(ProkerTtd::class, 'kegiatan_id');
    }

    /**
     * Ambil TTD untuk role tertentu.
     */
    public function ttdForRole(string $role): ?ProkerTtd
    {
        return $this->prokerTtd->where('role', $role)->first();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByTahun($query, int $tahun)
    {
        return $query->whereYear('tanggal_mulai', $tahun);
    }

    public function scopeByBidang($query, int $bidangId)
    {
        return $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $bidangId));
    }

    public function scopeByKategori($query, int $kategoriId)
    {
        return $query->whereHas('kategoris', fn($q) => $q->where('mk_kategori_kegiatan.id', $kategoriId));
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->banner) : null;
    }

    public function getSuratProkerUrlAttribute(): ?string
    {
        return $this->surat_proker ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->surat_proker) : null;
    }

    /**
     * URL PDF asli sebelum ada TTD — digunakan untuk regenerasi saat cancel/ulang TTD.
     */
    public function getSuratProkerOriginalUrlAttribute(): ?string
    {
        return $this->surat_proker_original
            ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->surat_proker_original)
            : $this->surat_proker_url; // fallback ke surat_proker jika belum ada original
    }


    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status ?? '');
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /**
     * Apakah proker ini masih dalam tahap perencanaan (Subbab 1)?
     */
    public function getIsPerencanaanAttribute(): bool
    {
        return in_array($this->status, self::STATUS_PERENCANAAN);
    }

    /**
     * Apakah proker ini sudah disetujui / dalam pelaksanaan (Subbab 2)?
     */
    public function getIsPelaksanaanAttribute(): bool
    {
        return in_array($this->status, self::STATUS_PELAKSANAAN);
    }

    /**
     * Apakah proker ini sudah selesai (Subbab 3)?
     */
    public function getIsSelesaiAttribute(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    /**
     * Sudah ada data realisasi yang diinput?
     */
    public function getHasRealisasiAttribute(): bool
    {
        return !is_null($this->realisasi_tanggal_mulai) || !is_null($this->catatan_pelaksanaan);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePerencanaan($query)
    {
        return $query->whereIn('status', self::STATUS_PERENCANAAN);
    }

    public function scopePelaksanaan($query)
    {
        return $query->whereIn('status', self::STATUS_PELAKSANAAN);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }
}