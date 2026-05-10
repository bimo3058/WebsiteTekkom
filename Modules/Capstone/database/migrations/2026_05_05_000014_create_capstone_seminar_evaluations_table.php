<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Seminar Evaluations (per-examiner evaluation untuk SEMPRO/EXPO)
 *
 * Source: 2026_02_22_100003_create_seminar_evaluations_table
 *
 * NOTE:
 * - examiner_id FK ke `lecturers` (bukan users seperti CTMS asli)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_seminar_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')
                ->constrained('capstone_seminar_schedules')->cascadeOnDelete();
            $table->foreignId('examiner_id')
                ->constrained('lecturers')->restrictOnDelete();
            $table->json('rubric_json')->nullable(); // snapshot rubric saat submission
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->default('PENDING'); // PENDING, SUBMITTED
            $table->timestamps();

            $table->unique(['schedule_id', 'examiner_id']); // one eval per examiner per schedule
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_seminar_evaluations');
    }
};
