<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bs_pendaftar_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_ujian_id')->constrained('bs_periode_ujians')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->string('nim');
            $table->string('nama_lengkap');
            $table->integer('semester_aktif');
            $table->string('target_wisuda')->nullable();
            $table->foreignId('dosen_pembimbing_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dosen_pembimbing_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status_pendaftaran', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('jadwal_ujian_id')->nullable()->constrained('bs_jadwal_ujians')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_pendaftar_ujians');
    }
};
