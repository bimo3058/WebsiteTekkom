<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Assessment Scores (skor per komponen dari evaluator)
 *
 * Source: 2026_02_27_000002_create_assessment_scores_table
 *
 * NOTE:
 * - evaluator_id FK ke `lecturers` (yg ngasih skor pasti dosen)
 * - student_id FK ke `students`
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')
                ->constrained('capstone_assessment_components')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()
                ->constrained('students')->nullOnDelete();
            $table->decimal('score', 5, 2);    // nilai 0-100
            $table->text('notes')->nullable();
            $table->string('evaluation_type'); // SEMPRO, SIDANG_TA, EXPO, BIMBINGAN
            $table->timestamps();

            $table->unique(['component_id', 'evaluator_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_assessment_scores');
    }
};
