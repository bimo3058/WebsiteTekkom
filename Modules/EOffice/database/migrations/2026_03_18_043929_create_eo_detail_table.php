<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── MANAJEMEN SURAT ─────────────────────────────────────
        // Isi dinamis surat (nilai per field template)
        Schema::create('eo_isi_surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_id');
            $table->unsignedBigInteger('field_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('surat_id')->references('id')->on('eo_surat')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('eo_field_template')->onDelete('cascade');
        });

        // Approval per surat (rekaman tiap langkah approval)
        Schema::create('eo_approval', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_id');
            $table->unsignedBigInteger('approver_id');
            $table->integer('urutan');
            $table->string('status')->comment('enum: menunggu, disetujui, ditolak, dilewati');
            $table->string('file_ttd')->nullable()->comment('path file tanda tangan');
            $table->string('role_approver');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal')->nullable();
            $table->timestamps();

            $table->foreign('surat_id')->references('id')->on('eo_surat')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Lampiran surat (file pendukung pengajuan)
        Schema::create('eo_lampiran', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_id');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('tipe_file')->comment('pdf, jpg, png, dll');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('surat_id')->references('id')->on('eo_surat')->onDelete('cascade');
        });

        // ─── PEMINJAMAN RUANGAN ───────────────────────────────────
        // Berkas pendukung peminjaman (surat izin, proposal, dll)
        Schema::create('eo_berkas_pendukung', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('peminjaman_id');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('tipe_file')->comment('pdf, jpg, png, dll');
            $table->integer('ukuran_file')->comment('dalam bytes');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('peminjaman_id')->references('id')->on('eo_peminjaman')->onDelete('cascade');
        });

        // Jadwal kuliah (untuk cek konflik ruangan otomatis)
        Schema::create('eo_import_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('admin_id');
            $table->string('nama_file')->comment('nama file Excel');
            $table->integer('jumlah_record');
            $table->string('status')->comment('enum: berhasil, gagal sebagian, gagal');
            $table->text('catatan')->nullable()->comment('error log');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('eo_jadwal_kuliah', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ruangan_id');
            $table->unsignedBigInteger('import_log_id')->nullable();
            $table->string('hari')->comment('Senin, Selasa, ..., Jumat');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('mata_kuliah');
            $table->string('dosen');
            $table->string('kelas');
            $table->string('semester');
            $table->string('tahun_ajaran')->comment('contoh: 2025/2026');
            $table->timestamps();

            $table->foreign('ruangan_id')->references('id')->on('eo_ruangan')->onDelete('cascade');
            $table->foreign('import_log_id')->references('id')->on('eo_import_log')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_jadwal_kuliah');
        Schema::dropIfExists('eo_import_log');
        Schema::dropIfExists('eo_berkas_pendukung');
        Schema::dropIfExists('eo_lampiran');
        Schema::dropIfExists('eo_approval');
        Schema::dropIfExists('eo_isi_surat');
    }
};