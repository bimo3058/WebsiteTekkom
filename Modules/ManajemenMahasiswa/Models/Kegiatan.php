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
        'kategori_kegiatan_id',
        'bidang_id',
        'lecturer_id',
        'ketua_student_id',
        'kepengurusan_id',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
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

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lecturer::class, 'lecturer_id');
    }

    public function ketuaStudent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'ketua_student_id');
    }

    public function kepengurusan(): BelongsTo
    {
        return $this->belongsTo(Kepengurusan::class, 'kepengurusan_id');
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
}