<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bidang/Unit tempat seorang pengurus himpunan bertugas (KABID, WAKABID, KANIT,
 * WAKANIT, Staff). Satu pengurus = satu bidang/unit, diisi dari form Ubah Role
 * di halaman Manajemen Pengguna.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mk_pengurus_bidang')) {
            return;
        }

        Schema::create('mk_pengurus_bidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('bidang_id')->constrained('mk_bidang')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_pengurus_bidang');
    }
};
