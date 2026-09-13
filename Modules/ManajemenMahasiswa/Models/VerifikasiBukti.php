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

    /**
     * Tautan untuk membuka berkas ini.
     *
     * Menunjuk ke route aplikasi, bukan ke URL publik Supabase: aksesnya
     * diperiksa dulu di VerifikasiController::bukti(), baru dialihkan ke tautan
     * bertanda tangan yang berumur pendek. URL publik sengaja tidak disediakan
     * lagi di sini — tautannya tidak pernah menanyakan siapa yang membuka,
     * sehingga sekali tersalin keluar, sertifikat mahasiswa ikut terbuka.
     */
    public function getUrlAksesAttribute(): string
    {
        return route('manajemenmahasiswa.verifikasi.bukti.show', $this->id);
    }

    public function isImage(): bool
    {
        return $this->tipe_file === self::TIPE_IMAGE;
    }
}
