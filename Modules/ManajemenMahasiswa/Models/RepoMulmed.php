<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class RepoMulmed extends Model
{
    use HasFactory;

    protected $table = 'mk_repo_mulmed';

    protected $fillable = [
        'kegiatan_id',
        'pengumuman_id',
        'nama_file',
        'path_file',
        'tipe_file',
        'jenis_dokumen',
        'dari_rencana',
        'judul_file',
        'deskripsi_meta',
        'visibility_status',
        'status_arsip',
    ];

    protected $casts = [
        // Salinan dokumen Rencana Proker saat Ajukan; terkunci di Pelaksanaan (S-59).
        'dari_rencana' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const VISIBILITY_PUBLIC   = 'public';
    const VISIBILITY_INTERNAL = 'internal';
    const VISIBILITY_PRIVATE  = 'private';

    const VISIBILITY_LIST = [
        self::VISIBILITY_PUBLIC,
        self::VISIBILITY_INTERNAL,
        self::VISIBILITY_PRIVATE,
    ];

    const ARSIP_AKTIF    = 'aktif';
    const ARSIP_DIARSIP  = 'diarsipkan';

    const TIPE_IMAGE    = 'image';
    const TIPE_VIDEO    = 'video';
    const TIPE_DOCUMENT = 'document';

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility_status', self::VISIBILITY_PUBLIC);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_arsip', self::ARSIP_AKTIF);
    }

    public function scopeByTipe(Builder $query, string $tipe): Builder
    {
        return $query->where('tipe_file', $tipe);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getUrlAttribute(): string
    {
        return app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->path_file);
    }

    /**
     * URL unduh dengan nama asli file. Objek di Supabase bernama UUID, dan atribut HTML
     * `download` diabaikan browser untuk link lintas domain, jadi nama dikirim lewat
     * parameter `?download=` (Supabase membalas Content-Disposition dengan nama ini).
     */
    public function getUrlUnduhAttribute(): string
    {
        $nama = $this->nama_file ?: basename($this->path_file);

        return $this->url . '?download=' . rawurlencode($nama);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    /**
     * Apakah file ini gambar? Dipakai untuk memisahkan galeri dari lampiran.
     *
     * Diperiksa dari ekstensi nama file, bukan kolom tipe_file, karena tipe_file
     * diisi dari mime-type saat unggah dan bisa kosong pada baris lama.
     */
    public function isGambar(): bool
    {
        return in_array(
            strtolower(pathinfo($this->nama_file ?? '', PATHINFO_EXTENSION)),
            ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            true
        );
    }
}