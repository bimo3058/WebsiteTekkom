<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: TA Submissions (submission Tugas Akhir mahasiswa)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_22_000004_create_ta_submissions_table
 * - 2026_02_27_000009_add_ta_defense_fields_to_ta_submissions_table
 *
 * NOTE:
 * - student_id FK ke `students` (bukan users seperti CTMS asli)
 * - reviewed_by tetap FK ke `users`
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_ta_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->string('status')->default('TA_LOCKED');
            // TA_LOCKED, TA_DRAFT, TA_REVISED, TA_READY, TA_REGISTERED, TA_DEFENDED
            $table->string('file_path')->nullable();
            $table->string('draft_report_path')->nullable();
            $table->string('paper_path')->nullable();
            $table->string('publication_link')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('student_id');
            $table->index(['group_id', 'status']); // expo eligibility check
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_submissions');
    }
};
