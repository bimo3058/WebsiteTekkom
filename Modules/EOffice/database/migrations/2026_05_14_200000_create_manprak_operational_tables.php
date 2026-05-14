<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modul per praktikum
        Schema::create('modul_praktikum', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->cascadeOnDelete();
        });

        // 2. Tugas per modul
        Schema::create('tugas_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // 3. Pengumpulan tugas oleh mahasiswa
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas_praktikum')->cascadeOnDelete();
            $table->uuid('daftar_praktikan_id');
            $table->string('file_path')->nullable();
            $table->text('catatan')->nullable();
            $table->float('nilai')->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->boolean('is_revision')->default(false);
            $table->timestamps();

            $table->foreign('daftar_praktikan_id')->references('id')->on('daftar_praktikan')->cascadeOnDelete();
        });

        // 4. Absensi per modul
        Schema::create('absensi_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
            $table->uuid('daftar_praktikan_id');
            $table->date('tanggal');
            $table->string('status')->default('hadir'); // hadir|izin|tidak_hadir
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('daftar_praktikan_id')->references('id')->on('daftar_praktikan')->cascadeOnDelete();
        });

        // 5. Nilai akhir per praktikan
        Schema::create('nilai_praktikum', function (Blueprint $table) {
            $table->id();
            $table->uuid('daftar_praktikan_id');
            $table->string('modul')->nullable();
            $table->float('nilai_tugas')->nullable();
            $table->float('nilai_absensi')->nullable();
            $table->float('nilai_akhir')->nullable();
            $table->timestamps();

            $table->foreign('daftar_praktikan_id')->references('id')->on('daftar_praktikan')->cascadeOnDelete();
        });

        // 6a. Asprak aktif per praktikum (tabel pivot global)
        Schema::create('asprak_praktikum', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('asprak'); // asprak|koor
            $table->text('deskripsi')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->cascadeOnDelete();
        });

        // 6b. Distribusi asprak ke modul
        Schema::create('modul_asprak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
            $table->foreignId('asprak_id')->constrained('asprak_praktikum')->cascadeOnDelete();
            $table->timestamps();
        });

        // 7. Materi / file per modul
        Schema::create('materi_modul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable();
            $table->string('tipe_file')->nullable();
            $table->timestamps();
        });

        // 8. Pengumuman per praktikum
        Schema::create('pengumuman_praktikum', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('konten');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman_praktikum');
        Schema::dropIfExists('materi_modul');
        Schema::dropIfExists('modul_asprak');
        Schema::dropIfExists('asprak_praktikum');
        Schema::dropIfExists('nilai_praktikum');
        Schema::dropIfExists('absensi_praktikum');
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('tugas_praktikum');
        Schema::dropIfExists('modul_praktikum');
    }
};
