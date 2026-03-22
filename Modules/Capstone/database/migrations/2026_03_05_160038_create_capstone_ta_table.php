<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capstone_ta_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->string('status')->default('TA_DRAFT');
            // TA_DRAFT, TA_REVISED, TA_READY, TA_REGISTERED, TA_DEFENDED
            $table->string('file_path')->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('capstone_ta_defense_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room')->nullable();
            $table->string('status')->default('PENDING_APPROVAL');
            // PENDING_APPROVAL, SCHEDULED, COMPLETED, CANCELLED
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('capstone_ta_defense_examiners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('capstone_ta_defense_schedules')->cascadeOnDelete();
            $table->foreignId('examiner_id')->constrained('lecturers')->cascadeOnDelete();
            $table->boolean('is_supervisor')->default(false);
            $table->timestamps();

            $table->unique(['schedule_id', 'examiner_id']);
        });

        Schema::create('capstone_ta_defense_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('capstone_ta_defense_schedules')->cascadeOnDelete();
            $table->foreignId('examiner_id')->constrained('lecturers')->cascadeOnDelete();
            $table->json('rubric_json')->nullable();
            $table->float('score')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, SUBMITTED
            $table->timestamps();

            $table->unique(['schedule_id', 'examiner_id']);
        });

        // Workflow log for ta_submissions status transitions
        Schema::create('capstone_ta_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ta_id')->constrained('capstone_ta_submissions')->cascadeOnDelete();
            $table->string('old_status');
            $table->string('new_status');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_workflow_logs');
        Schema::dropIfExists('capstone_ta_defense_evaluations');
        Schema::dropIfExists('capstone_ta_defense_examiners');
        Schema::dropIfExists('capstone_ta_defense_schedules');
        Schema::dropIfExists('capstone_ta_submissions');
    }
};