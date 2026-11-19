<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan metadata terstruktur ke catatan konseling GPM:
 *
 * - kategori_kasus  : Enum dropdown (kesehatan_mental, kekerasan_verbal,
 *                     kaderisasi, kekerasan_seksual, lainnya).
 * - kronologi       : Cerita kejadian dari POV mahasiswa (teks terenkripsi).
 * - keinginan_pelapor: Apa yang diinginkan pelapor (teks terenkripsi).
 * - tindak_lanjut   : Arahan atau langkah selanjutnya (teks terenkripsi).
 *
 * Kolom teks menggunakan cast `encrypted` di model sehingga bertipe TEXT
 * dan tidak bisa dicari via SQL. Semua kolom nullable agar backward-compatible
 * dengan catatan lama yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_catatan_konseling', function (Blueprint $table) {
            $table->string('kategori_kasus', 30)->nullable()->after('tanggal');
            $table->text('kronologi')->nullable()->after('kategori_kasus');
            $table->text('keinginan_pelapor')->nullable()->after('kronologi');
            $table->text('tindak_lanjut')->nullable()->after('keinginan_pelapor');
        });
    }

    public function down(): void
    {
        $kolom = array_values(array_filter(
            ['kategori_kasus', 'kronologi', 'keinginan_pelapor', 'tindak_lanjut'],
            fn (string $k) => Schema::hasColumn('mk_catatan_konseling', $k)
        ));

        if ($kolom !== []) {
            Schema::table('mk_catatan_konseling', function (Blueprint $table) use ($kolom) {
                $table->dropColumn($kolom);
            });
        }
    }
};
