<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom persetujuan admin dari mk_kegiatan.
     *
     * Kolom ini dibuat migrasi 2026_05_04_000001 untuk tahap "admin menyetujui /
     * menolak proker" yang tidak pernah dipakai: proker langsung masuk Pelaksanaan
     * begitu ketua menekan Ajukan Proker. Satu-satunya isi kolom ini adalah data
     * contoh 17 Sep 2026 (id 92–111); catatan_penolakan kosong di semua baris.
     *
     * down() hanya memulihkan struktur kolom, bukan isinya.
     */
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('mk_kegiatan', 'disetujui_oleh')) {
                $table->dropForeign(['disetujui_oleh']);
            }
        });

        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $kolom = array_values(array_filter(
                ['disetujui_oleh', 'disetujui_at', 'catatan_penolakan'],
                fn ($nama) => Schema::hasColumn('mk_kegiatan', $nama)
            ));

            if ($kolom) {
                $table->dropColumn($kolom);
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_kegiatan', 'disetujui_oleh')) {
                $table->unsignedBigInteger('disetujui_oleh')->nullable()
                      ->comment('FK users — admin yang approve proker');
                $table->foreign('disetujui_oleh')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('mk_kegiatan', 'disetujui_at')) {
                $table->timestamp('disetujui_at')->nullable();
            }
            if (!Schema::hasColumn('mk_kegiatan', 'catatan_penolakan')) {
                $table->text('catatan_penolakan')->nullable()
                      ->comment('alasan jika proker ditolak');
            }
        });
    }
};
