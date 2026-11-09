<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Daftar pengelola per kegiatan: siapa saja — selain pemiliknya — yang boleh
 * mengubah (dan opsional menghapus) satu kegiatan di ketiga subbab Manajemen
 * Kegiatan. Diatur lewat bagian "Akses Kelola" di form; aturannya ada di
 * KegiatanPolicy.
 *
 * Tanpa backfill: kegiatan lama tetap dikelola pemilik & override-nya
 * (admin trio + Ketua Himpunan) sampai daftarnya diisi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_kegiatan_pengelola', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('mk_kegiatan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // false = "Boleh edit", true = "Edit & hapus"
            $table->boolean('boleh_hapus')->default(false);
            $table->timestamps();

            // Satu orang hanya tercatat sekali per kegiatan
            $table->unique(['kegiatan_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_kegiatan_pengelola');
    }
};
