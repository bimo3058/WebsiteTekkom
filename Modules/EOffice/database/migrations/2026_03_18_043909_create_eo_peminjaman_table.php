<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── MANAJEMEN SURAT ─────────────────────────────────────
        // Konfigurasi alur approval per jenis surat
        Schema::create('eo_approval_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jenis_id');
            $table->integer('urutan');
            $table->string('role')->comment('role yang harus approve di urutan ini');
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->foreign('jenis_id')->references('id')->on('eo_jenis_surat')->onDelete('cascade');
        });

        // Surat utama
        Schema::create('eo_surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jenis_id');
            $table->unsignedBigInteger('pemohon_id')->comment('user yang mengajukan');
            $table->unsignedBigInteger('dibuat_oleh_admin')->nullable()->comment('admin yang memproses');
            $table->integer('nomor_urut')->nullable();
            $table->string('bulan_tahun')->nullable();
            $table->string('nomor_surat')->nullable()->unique();
            $table->date('tanggal_pengajuan');
            $table->string('status')->comment('enum: draft, diajukan, diproses, disetujui, ditolak, selesai');
            $table->string('file_hasil')->nullable()->comment('path file surat jadi');
            $table->string('tipe_pengajuan')->comment('enum: online, langsung');
            $table->text('catatan_pemohon')->nullable();
            $table->timestamps();

            $table->foreign('jenis_id')->references('id')->on('eo_jenis_surat')->onDelete('restrict');
            $table->foreign('pemohon_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dibuat_oleh_admin')->references('id')->on('users')->onDelete('set null');
        });

        // ─── PEMINJAMAN RUANGAN ───────────────────────────────────
        Schema::create('eo_peminjaman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('pemohon');
            $table->unsignedBigInteger('ruangan_id');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('nullable, di admin');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('keperluan');
            $table->string('status')->comment('enum: menunggu, disetujui, ditolak, dibatalkan, berlangsung, selesai');
            $table->text('alasan_penolakan')->nullable();
            $table->boolean('is_auto_approved')->default(false)->comment('true jika disetujui otomatis');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ruangan_id')->references('id')->on('eo_ruangan')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_peminjaman');
        Schema::dropIfExists('eo_surat');
        Schema::dropIfExists('eo_approval_config');
    }
};