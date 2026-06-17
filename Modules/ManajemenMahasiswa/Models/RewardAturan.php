<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Dokumen aturan reward prestasi (SK FT 774 / aturan terbaru) yang diunggah admin
 * dan dirujuk oleh mahasiswa & admin saat mengajukan / meninjau reward.
 */
class RewardAturan extends Model
{
    protected $table = 'mk_reward_aturan';

    protected $fillable = [
        'judul',
        'nama_file',
        'path_file',
        'tipe_file',
        'uploaded_by',
    ];

    const TIPE_IMAGE    = 'image';
    const TIPE_DOCUMENT = 'document';

    public function getPublicUrlAttribute(): string
    {
        return app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->path_file);
    }

    public function isImage(): bool
    {
        return $this->tipe_file === self::TIPE_IMAGE;
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
