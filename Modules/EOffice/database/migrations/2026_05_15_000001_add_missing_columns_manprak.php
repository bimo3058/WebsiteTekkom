<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom status pengumpulan tugas
        // (belum_dicek | revisi | acc) & status_pengumpulan lebih eksplisit
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            // status: belum_dicek | revisi | acc
            $table->string('status_pengumpulan')->default('belum_dicek')->after('is_revision');
        });

        // Tambah kolom approval nilai oleh koor & dosen
        Schema::table('nilai_praktikum', function (Blueprint $table) {
            $table->boolean('disetujui_koor')->default(false)->after('nilai_akhir');
            $table->boolean('disetujui_dosen')->default(false)->after('disetujui_koor');
            $table->boolean('dipublikasikan')->default(false)->after('disetujui_dosen');
        });

        // Tambah kolom kode di modul (untuk generate kode oleh koor)
        Schema::table('modul_praktikum', function (Blueprint $table) {
            $table->string('kode_modul')->nullable()->unique()->after('urutan');
            $table->string('jadwal_minggu')->nullable()->after('kode_modul');
        });

        // Kolom transkrip di pendaftaran asprak (compress)
        Schema::table('pendaftaran_asprak', function (Blueprint $table) {
            $table->string('transkrip_path')->nullable()->after('cv_path');
        });
    }

    public function down(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->dropColumn('status_pengumpulan');
        });
        Schema::table('nilai_praktikum', function (Blueprint $table) {
            $table->dropColumn(['disetujui_koor', 'disetujui_dosen', 'dipublikasikan']);
        });
        Schema::table('modul_praktikum', function (Blueprint $table) {
            $table->dropColumn(['kode_modul', 'jadwal_minggu']);
        });
        Schema::table('pendaftaran_asprak', function (Blueprint $table) {
            $table->dropColumn('transkrip_path');
        });
    }
};
