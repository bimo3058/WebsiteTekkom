<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mode simpan identitas (R-40): tiket anonim menyimpan akun pelapornya di kolom
 * terpisah, hanya untuk penanganan bila diperlukan. user_id tetap NULL, sehingga
 * tiket tidak muncul di daftar pelapor dan nama tidak tampil di layar pengelola.
 * Form memberi tahu pelapor bahwa identitas akun tetap disimpan sistem.
 *
 * Tiket anonim yang dibuat sebelum migrasi ini tetap NULL karena dikirim dengan
 * janji tidak tercatat atas nama pelapor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->unsignedBigInteger('pelapor_tersimpan_id')->nullable()->after('user_id')
                ->comment('akun pelapor tiket anonim; tidak ditampilkan di aplikasi');
            $table->foreign('pelapor_tersimpan_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->dropForeign(['pelapor_tersimpan_id']);
            $table->dropColumn('pelapor_tersimpan_id');
        });
    }
};
