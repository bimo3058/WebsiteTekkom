<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tahun ajaran per mata kuliah.
     *
     * Satu klaim bisa memuat beberapa MK yang diambil di semester berbeda,
     * jadi tahun ajaran tidak lagi menempel pada klaim (reward_tahun_ajaran)
     * melainkan pada tiap MK: {"Kalkulus": "2023-ganjil", ...}. Kunci peta
     * adalah nama MK yang sama dengan isi reward_mk_diajukan, yang tetap
     * menjadi sumber daftar MK untuk cek bentrok & kuota.
     *
     * reward_tahun_ajaran sengaja TIDAK dihapus di sini: kode di cabang lain
     * masih menulis kolom itu, dan menghapusnya dari database bersama akan
     * membuat pengajuan mereka gagal. Kolom itu tidak dipakai lagi.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('mk_prestasi', 'reward_mk_ta')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->json('reward_mk_ta')->nullable()->after('reward_mk_diajukan');
            });
        }

        // Klaim lama yang sudah punya satu tahun ajaran: nilainya diturunkan ke
        // tiap MK-nya, supaya tidak ada data yang hilang saat tampilan beralih.
        DB::table('mk_prestasi')
            ->whereNotNull('reward_mk_diajukan')
            ->whereNotNull('reward_tahun_ajaran')
            ->whereNull('reward_mk_ta')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $mk = json_decode($row->reward_mk_diajukan, true);
                    if (!is_array($mk) || empty($mk)) {
                        continue;
                    }

                    $peta = [];
                    foreach ($mk as $nama) {
                        $peta[$nama] = $row->reward_tahun_ajaran;
                    }

                    DB::table('mk_prestasi')->where('id', $row->id)->update([
                        'reward_mk_ta' => json_encode($peta, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_prestasi', 'reward_mk_ta')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->dropColumn('reward_mk_ta');
            });
        }
    }
};
