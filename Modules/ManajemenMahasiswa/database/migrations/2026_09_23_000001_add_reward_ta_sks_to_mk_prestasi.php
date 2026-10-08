<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ManajemenMahasiswa\Models\Prestasi;

return new class extends Migration
{
    /**
     * Tahun ajaran & bobot SKS yang benar-benar diklaim.
     *
     * reward_sks_diajukan disimpan, bukan dihitung ulang saat render, karena
     * bobot SKS tiap mata kuliah berasal dari konstanta kurikulum di model.
     * Begitu kurikulumnya diperbarui, total SKS klaim lama akan ikut bergeser
     * tanpa ada yang mengajukan atau menyetujui apa pun — persoalan yang sama
     * dengan kelompok kuota, dan diselesaikan dengan cara yang sama: dicap saat
     * pengajuan.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('mk_prestasi', 'reward_tahun_ajaran')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->string('reward_tahun_ajaran', 20)->nullable()->after('reward_sk_ref');
                $table->unsignedTinyInteger('reward_sks_diajukan')->nullable()->after('reward_sks_max');
            });
        }

        // Pengisian sekali jalan untuk klaim yang sudah ada. reward_tahun_ajaran
        // sengaja dibiarkan kosong: tidak ada sumber data yang bisa dipakai untuk
        // menebak semester mana yang dimaksud pemiliknya.
        $sksPerMk = Prestasi::mataKuliahFlat();

        DB::table('mk_prestasi')
            ->whereNotNull('reward_mk_diajukan')
            ->whereNull('reward_sks_diajukan')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($sksPerMk) {
                foreach ($rows as $row) {
                    $mk = json_decode($row->reward_mk_diajukan, true);
                    if (!is_array($mk) || empty($mk)) {
                        continue;
                    }

                    $total = 0;
                    foreach ($mk as $nama) {
                        $total += $sksPerMk[$nama] ?? 0;
                    }

                    DB::table('mk_prestasi')->where('id', $row->id)->update([
                        'reward_sks_diajukan' => $total,
                    ]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_prestasi', 'reward_tahun_ajaran')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->dropColumn(['reward_tahun_ajaran', 'reward_sks_diajukan']);
            });
        }
    }
};
