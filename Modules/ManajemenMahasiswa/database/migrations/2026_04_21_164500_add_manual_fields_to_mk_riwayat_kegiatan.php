<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            // Buat kegiatan_id nullable (agar bisa input manual tanpa referensi)
            $table->unsignedBigInteger('kegiatan_id')->nullable()->change();

            // Kolom manual: nama kegiatan bebas ketik
            $table->string('nama_kegiatan_manual', 255)->nullable()->after('kegiatan_id');

            // Kolom manual: peran bebas ketik (selain opsi bawaan)
            $table->string('peran_manual', 255)->nullable()->after('peran');

            // Tanggal kegiatan manual (opsional, untuk kegiatan di luar sistem)
            $table->date('tanggal_kegiatan')->nullable()->after('peran_manual');
        });
    }

    public function down(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            $table->dropColumn(['nama_kegiatan_manual', 'peran_manual', 'tanggal_kegiatan']);
        });
    }
};
