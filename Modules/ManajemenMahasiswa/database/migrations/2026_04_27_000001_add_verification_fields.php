<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom verifikasi ke mk_riwayat_kegiatan
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            $table->string('verification_status', 20)->default('pending')->after('tanggal_kegiatan');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_note')->nullable()->after('verified_at');

            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });

        // Tambah kolom verifikasi ke mk_prestasi
        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->string('verification_status', 20)->default('pending')->after('tahun');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_note')->nullable()->after('verified_at');

            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'verification_note']);
        });

        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'verification_note']);
        });
    }
};
