<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom nomor sertifikat & link verifikasi ke mk_prestasi
        // Untuk membantu admin membedakan sertifikat asli vs palsu (Request Bu Bellia)
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

    public function down(): void
    {
        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->dropColumn(['nomor_sertifikat', 'link_verifikasi']);
        });
    }
};
