<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ManajemenMahasiswa\Models\Prestasi;

return new class extends Migration
{
    /**
     * Membekukan dasar aturan pada tiap klaim reward.
     *
     * Sebelum ini, kelompok kuota dihitung ulang dari aturan yang berlaku saat
     * halaman dibuka. Akibatnya, begitu SK berganti dan pengelompokannya ikut
     * berubah, klaim lama akan berpindah kelompok secara surut — angka kuota
     * seorang mahasiswa bisa berubah sendiri tanpa ada yang menyetujui atau
     * membatalkan apa pun.
     *
     * Dua kolom di bawah mengunci kelompok kuota & SK rujukan saat klaim
     * diajukan, sama seperti jatah MK/SKS yang memang sudah disimpan per baris.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('mk_prestasi', 'reward_kuota_grup')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->string('reward_kuota_grup', 20)->nullable()->after('reward_is_invention');
                $table->string('reward_sk_ref', 60)->nullable()->after('reward_kuota_grup');
            });
        }

        // Pengisian sekali jalan untuk klaim yang sudah ada. Aturan yang berlaku
        // saat migrasi ini dijalankan masih SK yang sama dengan saat mereka
        // diajukan, jadi hasil perhitungannya memang cap yang benar.
        DB::table('mk_prestasi')
            ->whereNotNull('reward_penyelenggara')
            ->whereNull('reward_sk_ref')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('mk_prestasi')->where('id', $row->id)->update([
                        'reward_kuota_grup' => Prestasi::tentukanKuotaGrup(
                            $row->reward_penyelenggara,
                            (bool) $row->reward_is_invention
                        ),
                        'reward_sk_ref' => Prestasi::SK_BERLAKU,
                    ]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_prestasi', 'reward_kuota_grup')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->dropColumn(['reward_kuota_grup', 'reward_sk_ref']);
            });
        }
    }
};
