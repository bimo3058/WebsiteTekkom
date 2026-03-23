<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── MANAJEMEN SURAT ─────────────────────────────────────
        // Riwayat perubahan status surat (audit trail surat keluar)
        Schema::create('eo_riwayat_status_surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_id');
            $table->unsignedBigInteger('aktor_id')->comment('user yang mengubah status');
            $table->string('status_baru');
            $table->text('catatan')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->timestamps();

            $table->foreign('surat_id')->references('id')->on('eo_surat')->onDelete('cascade');
            $table->foreign('aktor_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Arsip surat keluar yang sudah selesai
        Schema::create('eo_arsip_surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_id');
            $table->string('kategori')->comment('arsip per kategori/jenis');
            $table->date('tanggal_arsip');
            $table->timestamps();

            $table->foreign('surat_id')->references('id')->on('eo_surat')->onDelete('cascade');
        });

        // Surat masuk dari eksternal
        Schema::create('eo_surat_masuk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nomor_surat_luar')->nullable()->comment('nomor surat dari pengirim');
            $table->string('pengirim');
            $table->string('perihal');
            $table->string('kategori')->nullable();
            $table->date('tanggal_surat');
            $table->date('tanggal_terima');
            $table->string('file_scan')->nullable()->comment('path file scan surat');
            $table->unsignedBigInteger('dicatat_oleh');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('dicatat_oleh')->references('id')->on('users')->onDelete('cascade');
        });

        // Disposisi surat masuk ke user/pejabat tertentu
        Schema::create('eo_disposisi_surat_masuk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_masuk_id');
            $table->unsignedBigInteger('penerima_id')->comment('user yang menerima disposisi');
            $table->boolean('status_baca')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_disposisi')->useCurrent();
            $table->timestamps();

            $table->foreign('surat_masuk_id')->references('id')->on('eo_surat_masuk')->onDelete('cascade');
            $table->foreign('penerima_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_disposisi_surat_masuk');
        Schema::dropIfExists('eo_surat_masuk');
        Schema::dropIfExists('eo_arsip_surat');
        Schema::dropIfExists('eo_riwayat_status_surat');
    }
};