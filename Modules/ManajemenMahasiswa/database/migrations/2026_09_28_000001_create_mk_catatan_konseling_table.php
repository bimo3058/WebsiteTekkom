<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Buku catatan konseling milik GPM (dosen konseling). Murni catatan pribadi
 * dosen: identitas mahasiswa diketik bebas dan sengaja TIDAK ditautkan ke akun
 * mahasiswa (keputusan pemilik modul 28 Sep 2026) — mahasiswa tetap menghubungi
 * dosen lewat chat seperti biasa, sistem hanya tempat dosen mencatat.
 *
 * `catatan` berisi teks terenkripsi (cast `encrypted` di model), jadi bertipe
 * text dan tidak bisa dicari.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_catatan_konseling', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mahasiswa', 150);
            $table->string('nim', 30)->nullable();
            $table->unsignedSmallInteger('angkatan')->nullable();
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_catatan_konseling');
    }
};
