<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notifikasi (shared antara surat & peminjaman)
        Schema::create('eo_notifikasi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('peminjaman_id')->nullable()->comment('nullable, bisa dari surat juga');
            $table->string('judul');
            $table->text('pesan');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('peminjaman_id')->references('id')->on('eo_peminjaman')->onDelete('set null');
        });

        // Log aktivitas user (shared antara surat & peminjaman)
        Schema::create('eo_log_aktivitas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('aksi')->comment('login, booking, approve, import, dll');
            $table->string('modul')->comment('peminjaman, surat, user, dll');
            $table->text('detail')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Audit log khusus modul EO (perubahan data penting)
        Schema::create('eo_audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('subject_type')->nullable()->comment('model yang diubah');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_audit_logs');
        Schema::dropIfExists('eo_log_aktivitas');
        Schema::dropIfExists('eo_notifikasi');
    }
};