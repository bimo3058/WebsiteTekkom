<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Titles (judul TA yang ditawarkan dosen / diusulkan mahasiswa)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_16_140416_create_titles_table
 * - 2026_02_16_144007_add_description_to_titles_table
 * - 2026_02_21_070000_add_proposal_fields_to_titles_table
 * - 2026_02_21_080000_add_specializations_to_titles_table
 *
 * NOTE:
 * - lecturer_id FK ke `lecturers` (bukan users seperti CTMS asli)
 * - proposed_supervisor_id FK ke `lecturers`
 * - proposed_by_group_id FK ke `capstone_groups`
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->text('problem_statement')->nullable();
            $table->text('scope')->nullable();
            $table->json('specializations')->nullable();

            $table->integer('quota')->default(1);
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
            $table->boolean('approved_by_admin')->default(false);

            // Title proposal flow (mahasiswa propose ke dosen)
            $table->string('title_source')->default('LECTURER'); // LECTURER, STUDENT
            $table->foreignId('proposed_by_group_id')->nullable()
                ->constrained('capstone_groups')->nullOnDelete();
            $table->foreignId('proposed_supervisor_id')->nullable()
                ->constrained('lecturers')->nullOnDelete();
            $table->string('supervisor_approval_status')->nullable(); // PENDING, APPROVED, REJECTED
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_titles');
    }
};
