<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEXES = [
        'bs_pertanyaan' => ['bs_question_course_status_idx', ['mk_id', 'status']],
        'bs_rps_detail' => ['bs_rps_detail_course_idx', ['mk_id']],
        'bs_dosen_pengampu_mk' => ['bs_lecturer_course_idx', ['user_id', 'mk_id']],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $table => [$name, $columns]) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($columns, $name));
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::INDEXES, true) as $table => [$name]) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
        }
    }
};
