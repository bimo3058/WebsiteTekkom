<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations - Add all foreign keys and indexes
     */
    public function up(): void
    {
        // HANYA JALANKAN DI POSTGRESQL
        if (DB::connection()->getDriverName() === 'pgsql') {

            /**
             * MODIFIKASI LOKAL: 
             * Melewati migrasi ini karena tabel 'bs_mata_kuliah_cpl' tidak ditemukan di repositori.
             * Ini memungkinkan modul utama praktikum tetap bisa berjalan.
             */
            return;

            // bs_mata_kuliah_cpl foreign keys
            Schema::table('bs_mata_kuliah_cpl', function (Blueprint $table) {
                $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('cpl_id')->references('id')->on('bs_cpl')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_cpl_cpmk foreign keys
            Schema::table('bs_cpl_cpmk', function (Blueprint $table) {
                $table->foreign('cpl_id')->references('id')->on('bs_cpl')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('cpmk_id')->references('id')->on('bs_cpmk')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_pertanyaan foreign keys
            Schema::table('bs_pertanyaan', function (Blueprint $table) {
                $table->foreign('cpl_id')->references('id')->on('bs_cpl')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_jawaban foreign keys
            Schema::table('bs_jawaban', function (Blueprint $table) {
                $table->foreign('soal_id')->references('id')->on('bs_pertanyaan')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_dosen_pengampu_mk foreign keys
            Schema::table('bs_dosen_pengampu_mk', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_rps_assign foreign keys
            Schema::table('bs_rps_assign', function (Blueprint $table) {
                $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->restrictOnDelete()->cascadeOnUpdate();
                $table->foreign('dosen_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            });

            // bs_rps_detail foreign keys
            Schema::table('bs_rps_detail', function (Blueprint $table) {
                $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_rps_cpl foreign keys
            Schema::table('bs_rps_cpl', function (Blueprint $table) {
                $table->foreign('rps_id')->references('id')->on('bs_rps_detail')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('cpl_id')->references('id')->on('bs_cpl')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_rps_cpmk foreign keys
            Schema::table('bs_rps_cpmk', function (Blueprint $table) {
                $table->foreign('rps_id')->references('id')->on('bs_rps_detail')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('cpmk_id')->references('id')->on('bs_cpmk')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_rps_dosen foreign keys
            Schema::table('bs_rps_dosen', function (Blueprint $table) {
                $table->foreign('rps_id')->references('id')->on('bs_rps_detail')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('dosen_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_rps_review foreign keys
            Schema::table('bs_rps_review', function (Blueprint $table) {
                $table->foreign('rps_id')->references('id')->on('bs_rps_assign')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_hasil_review_rps foreign keys
            Schema::table('bs_hasil_review_rps', function (Blueprint $table) {
                $table->foreign('rps_detail_id')->references('id')->on('bs_rps_review')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('parameter_id')->references('id')->on('bs_parameter')->restrictOnDelete()->cascadeOnUpdate();
            });

            // bs_kompre_session foreign keys
            Schema::table('bs_kompre_session', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_kompre_jawaban foreign keys
            Schema::table('bs_kompre_jawaban', function (Blueprint $table) {
                $table->foreign('kompre_session_id')->references('id')->on('bs_kompre_session')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('pertanyaan_id')->references('id')->on('bs_pertanyaan')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('jawaban_dipilih')->references('id')->on('bs_jawaban')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_jadwal_ujians foreign keys
            Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
                $table->foreign('periode_ujian_id')->references('id')->on('bs_periode_ujians')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_pendaftar_ujians foreign keys
            Schema::table('bs_pendaftar_ujians', function (Blueprint $table) {
                $table->foreign('periode_ujian_id')->references('id')->on('bs_periode_ujians')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('mahasiswa_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('dosen_pembimbing_1_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('dosen_pembimbing_2_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('jadwal_ujian_id')->references('id')->on('bs_jadwal_ujians')->nullOnDelete()->cascadeOnUpdate();
            });

            // bs_review foreign keys
            Schema::table('bs_review', function (Blueprint $table) {
                $table->foreign('pertanyaan_id')->references('id')->on('bs_pertanyaan')->cascadeOnUpdate()->cascadeOnDelete();
            });

            // bs_audit_logs indexes
            Schema::table('bs_audit_logs', function (Blueprint $table) {
                $table->index(['subject_type', 'subject_id']);
                $table->index('created_at');
            });

            // bs_periode_ujians indexes
            Schema::table('bs_periode_ujians', function (Blueprint $table) {
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Biarkan kosong untuk menghindari error rollback pada tabel yang tidak eksis
        }
    }
};