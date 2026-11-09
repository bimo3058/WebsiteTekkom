<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Merapikan kolom `tahun` yang masih NULL padahal `tanggal_mulai` sudah terisi.
 *
 * Kegiatan lama dari alur Proker → Pelaksanaan tersimpan tanpa `tahun` karena
 * kolom itu dulu tidak ikut diisi. Akibatnya kegiatan tersebut lenyap dari
 * daftar begitu user memilih tahun mana pun di filter Pelaksanaan & Arsip.
 *
 * Aplikasinya sendiri sudah tidak bergantung pada migrasi ini — Kegiatan::
 * scopeFilterTahun() dan daftarTahun() memakai `tanggal_mulai` sebagai cadangan.
 * Migrasi ini murni membereskan datanya supaya nilai tersimpannya ikut benar.
 *
 * Kegiatan yang `tanggal_mulai`-nya juga kosong sengaja dilewati: tahunnya
 * memang tidak diketahui dan tidak boleh ditebak.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('mk_kegiatan')
            ->whereNull('tahun')
            ->whereNotNull('tanggal_mulai')
            ->update([
                'tahun' => DB::raw('EXTRACT(YEAR FROM tanggal_mulai)'),
            ]);
    }

    public function down(): void
    {
        // Tidak dikembalikan: nilai NULL sebelumnya adalah datanya yang rusak,
        // dan mengosongkan ulang `tahun` justru memunculkan kembali bugnya.
    }
};
