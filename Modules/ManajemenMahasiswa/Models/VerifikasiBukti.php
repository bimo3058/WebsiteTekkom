<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerifikasiBukti extends Model
{
    protected $table = 'mk_verifikasi_bukti';

    protected $fillable = [
        'bukti_type',
        'bukti_id',
        'nama_file',
        'path_file',
        'tipe_file',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const TYPE_RIWAYAT  = 'riwayat';
    const TYPE_PRESTASI = 'prestasi';

    const TIPE_IMAGE    = 'image';
    const TIPE_DOCUMENT = 'document';

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getPublicUrlAttribute(): string
    {
        return app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->path_file);
    }

    public function isImage(): bool
    {
        return $this->tipe_file === self::TIPE_IMAGE;
    }
}
