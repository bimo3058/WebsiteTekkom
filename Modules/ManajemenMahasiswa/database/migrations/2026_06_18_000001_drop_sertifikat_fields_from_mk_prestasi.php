<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom nomor_sertifikat & link_verifikasi dari mk_prestasi.
     * Form prestasi kini mewajibkan upload 1 PDF bukti, sehingga sertifikat
     * selalu terlampir & kedua field ini menjadi redundan (sejajar GForm).
     */
    public function up(): void
    {
        Schema::table('mk_prestasi', function (Blueprint $table) {
            if (Schema::hasColumn('mk_prestasi', 'nomor_sertifikat')) {
                $table->dropColumn('nomor_sertifikat');
            }
            if (Schema::hasColumn('mk_prestasi', 'link_verifikasi')) {
                $table->dropColumn('link_verifikasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_prestasi', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_prestasi', 'nomor_sertifikat')) {
                $table->string('nomor_sertifikat', 100)->nullable()->after('verification_note')
                    ->comment('Nomor sertifikat resmi (wajib untuk Belmawa/Puspresnas)');
            }
            if (!Schema::hasColumn('mk_prestasi', 'link_verifikasi')) {
                $table->string('link_verifikasi', 500)->nullable()->after('nomor_sertifikat')
                    ->comment('URL halaman verifikasi sertifikat resmi penyelenggara');
            }
        });
    }
};
