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
        'judul_file',
        'deskripsi_meta',
        'visibility_status',
        'status_arsip',
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
        return \Storage::url($this->path_file);
    }
}