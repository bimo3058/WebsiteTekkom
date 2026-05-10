<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Group Supervisor Proposals (usulan supervisor dari mahasiswa)
 *
 * Source: 2026_02_22_000002_create_group_supervisor_proposals_table
 *
 * NOTE:
 * - proposed_supervisor_*_id FK ke `lecturers` (bukan users seperti CTMS asli)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_group_supervisor_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('proposed_supervisor_1_id')
                ->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('proposed_supervisor_2_id')->nullable()
                ->constrained('lecturers')->nullOnDelete();
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_group_supervisor_proposals');
    }
};
