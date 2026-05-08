<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Evaluations (legacy generic evaluation table)
 *
 * Source: 2026_02_16_140434_create_evaluations_table
 *
 * NOTE:
 * - evaluator_id FK ke `lecturers` (krn yang evaluasi pasti dosen)
 * - student_id FK ke `students`
 *
 * CTMS masih punya tabel ini buat backward compat (dipakai EvaluationController).
 * Harusnya seiring waktu dimigrasiin ke seminar_evaluations & ta_defense_evaluations.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()
                ->constrained('students')->nullOnDelete();
            $table->string('type'); // SEMPRO, SIDANG, EXPO
            $table->decimal('score', 5, 2);
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_evaluations');
    }
};
