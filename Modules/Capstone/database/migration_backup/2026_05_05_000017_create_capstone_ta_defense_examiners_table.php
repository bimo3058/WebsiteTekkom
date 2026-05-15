<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: TA Defense Examiners (penguji untuk sidang TA)
 *
 * Source: 2026_02_22_100005_create_ta_defense_examiners_table
 *
 * NOTE:
 * - examiner_id FK ke `lecturers` (bukan users seperti CTMS asli)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_ta_defense_examiners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')
                ->constrained('capstone_ta_defense_schedules')->cascadeOnDelete();
            $table->foreignId('examiner_id')
                ->constrained('lecturers')->restrictOnDelete();
            $table->string('role'); // SUPERVISOR_1, SUPERVISOR_2, EXAMINER_1, EXAMINER_2
            $table->timestamps();

            $table->unique(['schedule_id', 'role']);        // one person per role per schedule
            $table->unique(['schedule_id', 'examiner_id']); // no duplicate examiner per schedule
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_defense_examiners');
    }
};
