<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Grade Consistency Checks
 * (cek konsistensi nilai antar fase PDC1 vs PDC2)
 *
 * Source: 2026_02_27_000005_create_grade_consistency_checks_table
 *
 * NOTE:
 * - student_id FK ke `students`
 * - checked_by tetap FK ke `users` (admin yg cek)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_grade_consistency_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()
                ->constrained('students')->nullOnDelete();
            $table->decimal('pdc1_score', 5, 2)->nullable();
            $table->decimal('pdc2_score', 5, 2)->nullable();
            $table->decimal('deviation', 5, 2)->nullable();
            $table->string('status')->default('UNCHECKED'); // UNCHECKED, CONSISTENT, INCONSISTENT
            $table->text('notes')->nullable();
            $table->foreignId('checked_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_grade_consistency_checks');
    }
};
