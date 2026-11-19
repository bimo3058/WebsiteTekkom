<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $t) {
            $t->unsignedTinyInteger('bobot_gabungan_modul')->nullable();
            $t->unsignedTinyInteger('bobot_gabungan_ta')->nullable();
            $t->unsignedInteger('bobot_gabungan_version')->default(0);
            $t->uuid('final_grade_batch_id')->nullable();
        });
        Schema::create('eo_prak_grade_policies', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->unsignedInteger('version')->unique();
            $t->string('nama');
            $t->json('rules');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamp('created_at');
        });
        Schema::create('eo_prak_grade_settings', function (Blueprint $t) {
            $t->unsignedInteger('id')->primary();
            $t->uuid('active_policy_id');
        });
        Schema::create('eo_prak_grade_batches', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('praktikum_id')->index();
            $t->uuid('policy_id');
            $t->char('fingerprint', 64);
            $t->string('status')->default('pending');
            $t->json('weights');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamp('created_at');
            $t->timestamp('published_at')->nullable();
        });
        Schema::create('eo_prak_final_results', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('batch_id')->index();
            $t->uuid('praktikan_id');
            $t->unsignedSmallInteger('cohort_year');
            $t->decimal('module_value', 12, 8);
            $t->decimal('ta_value', 12, 8);
            $t->decimal('final_value', 5, 2);
            $t->string('letter', 4);
            $t->decimal('points', 3, 2);
            $t->json('calculation');
            $t->unique(['batch_id', 'praktikan_id']);
        });
        $bands = function (array $items) {
            return array_map(fn ($v) => ['minimum' => (string) $v[0], 'letter' => $v[1], 'points' => $v[2]], $items);
        };
        $id = (string) Str::uuid();
        DB::table('eo_prak_grade_policies')->insert(['id' => $id, 'version' => 1, 'nama' => 'Predikat Teknik Komputer — Angkatan 2023 dan 2024', 'rules' => json_encode([
            ['from' => 2000, 'to' => 2023, 'bands' => $bands([[80, 'A', '4.00'], [70, 'B', '3.00'], [60, 'C', '2.00'], [51, 'D', '1.00'], [0, 'E', '0.00']])],
            ['from' => 2024, 'to' => null, 'bands' => $bands([[85, 'A', '4.00'], [80, 'AB', '3.50'], [75, 'B', '3.00'], [70, 'BC', '2.50'], [60, 'C', '2.00'], [40, 'D', '1.00'], [0, 'E', '0.00']])],
        ]), 'created_at' => now()]);
        DB::table('eo_prak_grade_settings')->insert(['id' => 1, 'active_policy_id' => $id]);
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_prak_final_results');
        Schema::dropIfExists('eo_prak_grade_batches');
        Schema::dropIfExists('eo_prak_grade_settings');
        Schema::dropIfExists('eo_prak_grade_policies');
        Schema::table('eo_praktikum', fn (Blueprint $t) => $t->dropColumn(['bobot_gabungan_modul', 'bobot_gabungan_ta', 'bobot_gabungan_version', 'final_grade_batch_id']));
    }
};
