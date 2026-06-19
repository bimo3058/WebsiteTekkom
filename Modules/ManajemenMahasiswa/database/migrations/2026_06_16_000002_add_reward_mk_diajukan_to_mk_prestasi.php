<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom usulan mata kuliah dari mahasiswa saat mengajukan reward
     * (SK FT 774 — mahasiswa mengusulkan MK yang nilainya dinaikkan; keputusan
     * final tetap di departemen via reward_mk_disetujui).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('mk_prestasi', 'reward_mk_diajukan')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->text('reward_mk_diajukan')->nullable()->after('reward_sks_max');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_prestasi', 'reward_mk_diajukan')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->dropColumn('reward_mk_diajukan');
            });
        }
    }
};
