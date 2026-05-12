<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_kategori_kegiatan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_kategori', 255);
            $table->timestamps();
        });

        Schema::create('mk_bidang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_bidang', 255);
            $table->timestamps();
        });

        Schema::create('mk_kepengurusan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tahun_periode', 20);
            $table->timestamps();
        });

        Schema::create('mk_kegiatan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('pembuat');
            $table->unsignedBigInteger('kategori_kegiatan_id');
            $table->unsignedBigInteger('bidang_id');
            $table->unsignedBigInteger('kepengurusan_id')->nullable();
            $table->string('judul', 255);
            $table->text('deskripsi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('banner', 255)->nullable()->comment('path gambar banner');
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->string('penanggung_jawab', 255)->nullable();
            $table->integer('target_peserta')->nullable()->comment('maks peserta');
            $table->string('status', 50)->comment('enum: akan_datang, berlangsung, selesai');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('kategori_kegiatan_id')->references('id')->on('mk_kategori_kegiatan')->onDelete('restrict');
            $table->foreign('bidang_id')->references('id')->on('mk_bidang')->onDelete('restrict');
            $table->foreign('kepengurusan_id')->references('id')->on('mk_kepengurusan')->onDelete('set null');
        });

        Schema::create('mk_kegiatan_peserta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status_kehadiran', 50)->default('terdaftar')->comment('enum: terdaftar, hadir, tidak_hadir');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('mk_kegiatan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_kegiatan_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->integer('file_size');
            $table->string('tipe', 50)->comment('enum: proposal, poster, lainnya');
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('mk_kegiatan')->onDelete('cascade');
        });

        Schema::create('mk_riwayat_kegiatan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->string('peran', 255);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('kegiatan_id')->references('id')->on('mk_kegiatan')->onDelete('cascade');
        });

        Schema::create('mk_pengurus_himaskom', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('kepengurusan_id');
            $table->string('divisi', 255);
            $table->string('jabatan', 255);
            $table->string('status_keaktifan', 50)->comment('enum: aktif, non-aktif');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('kepengurusan_id')->references('id')->on('mk_kepengurusan')->onDelete('cascade');
        });

        Schema::create('mk_repo_mulmed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kegiatan_id')->nullable();
            $table->unsignedBigInteger('pengumuman_id')->nullable();
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->string('tipe_file', 100);
            $table->string('judul_file', 255);
            $table->text('deskripsi_meta')->nullable();
            $table->string('visibility_status', 50);
            $table->string('status_arsip', 50);
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('mk_kegiatan')->onDelete('set null');
            $table->foreign('pengumuman_id')->references('id')->on('mk_pengumuman')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_repo_mulmed');
        Schema::dropIfExists('mk_pengurus_himaskom');
        Schema::dropIfExists('mk_riwayat_kegiatan');
        Schema::dropIfExists('mk_kegiatan_attachments');
        Schema::dropIfExists('mk_kegiatan_peserta');
        Schema::dropIfExists('mk_kegiatan');
        Schema::dropIfExists('mk_kepengurusan');
        Schema::dropIfExists('mk_bidang');
        Schema::dropIfExists('mk_kategori_kegiatan');
    }
};