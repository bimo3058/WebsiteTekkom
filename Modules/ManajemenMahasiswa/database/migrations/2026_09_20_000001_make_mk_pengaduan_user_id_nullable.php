<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tiket konfidensial tidak boleh menyimpan identitas pelapor.
 *
 * Sebelumnya user_id selalu terisi, dan anonimitas hanya berupa penyamaran di
 * tampilan — siapa pun yang bisa membaca tabel dapat menelusuri pelapornya.
 * Kolom dibuat nullable agar tiket konfidensial bisa disimpan tanpa user_id,
 * dan FK diganti ke SET NULL (bukan CASCADE) supaya tiket tidak ikut terhapus
 * bila akun pelapor dihapus.
 *
 * Migrasi ini hanya mengubah skema. Pengosongan user_id pada tiket
 * konfidensial yang SUDAH ada dilakukan terpisah (lihat catatan rilis).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE mk_pengaduan ALTER COLUMN user_id DROP NOT NULL');

        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('mk_pengaduan')->whereNull('user_id')->exists()) {
            throw new RuntimeException(
                'Tidak bisa rollback: masih ada tiket konfidensial dengan user_id NULL. '
                . 'Mengisinya kembali akan merusak anonimitas, jadi lakukan secara manual bila memang perlu.'
            );
        }

        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE mk_pengaduan ALTER COLUMN user_id SET NOT NULL');

        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
