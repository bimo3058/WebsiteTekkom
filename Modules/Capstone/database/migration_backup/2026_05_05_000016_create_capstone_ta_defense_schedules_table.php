<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: TA Defense Schedules (jadwal sidang TA per mahasiswa)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_22_100004_create_ta_defense_schedules_table
 * - 2026_02_22_200001_add_schedule_request_fields (parsial — yg untuk TA defense)
 *
 * NOTE:
 * - student_id FK ke `students` (bukan users seperti CTMS asli)
 * - requested_by tetap FK ke `users`
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_ta_defense_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->string('status')->default('SCHEDULED'); // SCHEDULED, COMPLETED, CANCELLED
            $table->foreignId('requested_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique('student_id'); // one defense per student
            $table->index(['date', 'start_time', 'end_time']); // for overlap queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_defense_schedules');
    }
};
