<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Seminar Schedules (jadwal SEMPRO & EXPO)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_22_100002_create_seminar_schedules_table
 * - 2026_02_22_200001_add_schedule_request_fields (parsial — yg untuk seminar)
 *
 * NOTE:
 * - examiner_*_id FK ke `lecturers` (bukan users seperti CTMS asli)
 * - requested_by tetap FK ke `users` (siapapun bisa request)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_seminar_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->string('type'); // SEMPRO, EXPO
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->foreignId('examiner_1_id')
                ->constrained('lecturers')->restrictOnDelete();
            $table->foreignId('examiner_2_id')
                ->constrained('lecturers')->restrictOnDelete();
            $table->string('status')->default('SCHEDULED'); // SCHEDULED, COMPLETED, CANCELLED
            $table->foreignId('requested_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'type']); // one SEMPRO and one EXPO per group
            $table->index(['date', 'start_time', 'end_time']); // for overlap queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_seminar_schedules');
    }
};
