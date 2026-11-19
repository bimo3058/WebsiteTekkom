<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * S-91 (15 Nov 2026): status tinjauan "Perlu Perbaikan" (`perbaikan`) digabung ke
 * "Menunggu Dilengkapi" (`dilengkapi`). Penanda "dikembalikan KABID" kini berasal dari
 * catatan terbuka (mk_kegiatan_catatan.diperbaiki_at NULL), jadi catatan tidak disentuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('mk_kegiatan')
            ->where('status_tinjauan', 'perbaikan')
            ->update(['status_tinjauan' => 'dilengkapi']);
    }

    public function down(): void
    {
        // Tidak dibalik: nilai lama 'perbaikan' sudah tidak dikenal kode.
    }
};
