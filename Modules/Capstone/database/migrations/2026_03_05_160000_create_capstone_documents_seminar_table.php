<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capstone_phase_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->string('phase'); // PDC1, SEMPRO, PDC2, TA
            $table->string('document_type');
            $table->string('name');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        Schema::create('capstone_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('phase'); // PDC1, PDC2
            $table->string('document_type')->default('report');
            $table->string('file_path');
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('capstone_seminar_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->string('type'); // SEMPRO, EXPO
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room')->nullable();
            $table->string('status')->default('PENDING_APPROVAL');
            // PENDING_APPROVAL, SCHEDULED, COMPLETED, CANCELLED
            $table->unsignedBigInteger('examiner_1_id')->nullable();
            $table->unsignedBigInteger('examiner_2_id')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('examiner_1_id')->references('id')->on('lecturers')->nullOnDelete();
            $table->foreign('examiner_2_id')->references('id')->on('lecturers')->nullOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('capstone_seminar_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('capstone_seminar_schedules')->cascadeOnDelete();
            $table->foreignId('examiner_id')->constrained('lecturers')->cascadeOnDelete();
            $table->json('rubric_json')->nullable();
            $table->float('score')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, SUBMITTED
            $table->timestamps();

            $table->unique(['schedule_id', 'examiner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_seminar_evaluations');
        Schema::dropIfExists('capstone_seminar_schedules');
        Schema::dropIfExists('capstone_documents');
        Schema::dropIfExists('capstone_phase_document_requirements');
    }
};