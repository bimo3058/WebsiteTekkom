<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan snapshot aturan kelulusan yang dipakai saat sesi ujian
     * dimulai, sehingga perubahan setting tidak mengubah hasil lama.
     */
    public function up(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->string('passing_rule')->nullable()->after('status');
        });

        // Sesi CBT lama semuanya dievaluasi dengan aturan "semua CPL minimal 6".
        // Data import lama (source import_*) tetap null dan ditandai historis.
        if (Schema::hasColumn('bs_kompre_session', 'source')) {
            DB::table('bs_kompre_session')
                ->whereNull('passing_rule')
                ->where(function ($query) {
                    $query->where('source', 'cbt')->orWhereNull('source');
                })
                ->update(['passing_rule' => 'cpl_all_min']);
        }
    }

    public function down(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->dropColumn('passing_rule');
        });
    }
};
