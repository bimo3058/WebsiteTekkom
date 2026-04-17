<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'kepengurusan_id',
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'jam_mulai',
        'tanggal_selesai',
        'jam_selesai',
        'lokasi',
        'banner',
        'anggaran',
        'penanggung_jawab',
        'ketua_pelaksana_id',
        'dosen_pendamping_id',
        'target_peserta',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'anggaran' => 'decimal:2',
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

    const STATUS_AKAN_DATANG = 'akan_datang';
    const STATUS_BERLANGSUNG = 'berlangsung';
    const STATUS_SELESAI = 'selesai';

    const STATUS_LIST = [
        self::STATUS_AKAN_DATANG,
        self::STATUS_BERLANGSUNG,
        self::STATUS_SELESAI,
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

    public function kepengurusan(): BelongsTo
    {
        return $this->belongsTo(Kepengurusan::class, 'kepengurusan_id');
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

    public function riwayatKegiatan(): HasMany
    {
        return $this->hasMany(RiwayatKegiatan::class, 'kegiatan_id');
    }

    public function repoMulmed(): HasMany
    {
        return $this->hasMany(RepoMulmed::class, 'kegiatan_id');
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
        return $query->where('bidang_id', $bidangId);
    }

    public function scopeByKategori($query, int $kategoriId)
    {
        return $query->where('kategori_kegiatan_id', $kategoriId);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? \Storage::url($this->banner) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AKAN_DATANG => 'Akan Datang',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            self::STATUS_SELESAI => 'Selesai',
            default => ucfirst($this->status ?? ''),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AKAN_DATANG => 'warning',
            self::STATUS_BERLANGSUNG => 'primary',
            self::STATUS_SELESAI => 'success',
            default => 'secondary',
        };
    }
}