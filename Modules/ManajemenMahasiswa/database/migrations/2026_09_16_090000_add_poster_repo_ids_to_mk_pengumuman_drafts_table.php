<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menyimpan daftar ID gambar pengumuman beserta urutannya (indeks 0 = cover).
     * Kolom lama `poster_repo_id` dibiarkan agar draf yang sudah ada tetap terbaca.
     */
    public function up(): void
    {
        Schema::table('mk_pengumuman_drafts', function (Blueprint $table) {
            $table->json('poster_repo_ids')->nullable()->after('poster_repo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mk_pengumuman_drafts', function (Blueprint $table) {
            $table->dropColumn('poster_repo_ids');
        });
    }
};
