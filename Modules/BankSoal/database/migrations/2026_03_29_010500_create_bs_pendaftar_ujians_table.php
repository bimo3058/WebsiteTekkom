<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_pendaftar_ujians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periode_ujian_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->string('nim');
            $table->string('nama_lengkap');
            $table->integer('semester_aktif');
            $table->string('target_wisuda')->nullable();
            $table->unsignedBigInteger('dosen_pembimbing_1_id')->nullable();
            $table->unsignedBigInteger('dosen_pembimbing_2_id')->nullable();
            $table->unsignedBigInteger('jadwal_ujian_id')->nullable();
            $table->string('status_pendaftaran')->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_pendaftar_ujians');
    }
};
