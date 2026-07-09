<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_pengaduan_delegasi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengaduan_id');
            $table->unsignedBigInteger('delegated_by')->comment('admin yang mendelegasikan');
            $table->unsignedBigInteger('delegated_to')->comment('dosen yang menerima');
            $table->text('notes_admin')->comment('catatan wajib dari admin saat delegasi');
            $table->string('status', 20)->default('aktif')->comment('aktif | ditanggapi | ditolak');
            $table->text('tanggapan')->nullable()->comment('respons dosen');
            $table->text('notes_balik')->nullable()->comment('catatan dosen saat kirim balik ke admin');
            $table->text('alasan_tolak')->nullable()->comment('alasan jika dosen menolak');
            $table->timestamp('delegated_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            $table->foreign('pengaduan_id')->references('id')->on('mk_pengaduan')->onDelete('cascade');
            $table->foreign('delegated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('delegated_to')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_pengaduan_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengaduan_id');
            $table->unsignedBigInteger('actor_user_id')->nullable()->comment('null = sistem/otomatis');
            $table->string('action', 50)->comment('dibuat|dibaca|didelegasikan|ditanggapi_dosen|ditolak_dosen|dijawab|diajukan_ulang|ditutup_mahasiswa|ditutup_admin|ditutup_otomatis');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('pengaduan_id')->references('id')->on('mk_pengaduan')->onDelete('cascade');
            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_pengaduan_log');
        Schema::dropIfExists('mk_pengaduan_delegasi');
    }
};
