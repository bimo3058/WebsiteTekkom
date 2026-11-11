<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed default syarat kelulusan ujian komprehensif.
     *
     * kompre_passing_rule: cpl_all_min | total_min
     * kompre_cpl_min_score: ambang nilai per CPL (tetap 6)
     * kompre_total_min_score: ambang nilai total (tetap 60)
     */
    public function up(): void
    {
        $now = now();

        DB::table('bs_pengaturan')->updateOrInsert(
            ['kunci' => 'kompre_passing_rule'],
            ['nilai' => 'cpl_all_min', 'created_at' => $now, 'updated_at' => $now]
        );

        DB::table('bs_pengaturan')->updateOrInsert(
            ['kunci' => 'kompre_cpl_min_score'],
            ['nilai' => '6', 'created_at' => $now, 'updated_at' => $now]
        );

        DB::table('bs_pengaturan')->updateOrInsert(
            ['kunci' => 'kompre_total_min_score'],
            ['nilai' => '60', 'created_at' => $now, 'updated_at' => $now]
        );
    }

    public function down(): void
    {
        DB::table('bs_pengaturan')->whereIn('kunci', [
            'kompre_passing_rule',
            'kompre_cpl_min_score',
            'kompre_total_min_score',
        ])->delete();
    }
};
