<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Draft Rencana Proker boleh disimpan tanpa kategori (S-57): kelengkapannya baru
 * ditagih saat Ajukan. Kolom FK lama `kategori_kegiatan_id` masih NOT NULL di
 * database — migrasi 2026_04_16 yang seharusnya melonggarkannya tidak berdampak —
 * jadi draft tanpa kategori gagal disimpan. Kategori sebenarnya ada di pivot
 * mk_kegiatan_kategori; kolom ini hanya salinan elemen pertama.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE mk_kegiatan ALTER COLUMN kategori_kegiatan_id DROP NOT NULL');
    }

    public function down(): void
    {
        // Hanya bisa dikembalikan bila tidak ada draft tanpa kategori
        if (!DB::table('mk_kegiatan')->whereNull('kategori_kegiatan_id')->exists()) {
            DB::statement('ALTER TABLE mk_kegiatan ALTER COLUMN kategori_kegiatan_id SET NOT NULL');
        }
    }
};
