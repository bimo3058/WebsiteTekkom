<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Supervisions (assignment supervisor ke group — source of truth)
 *
 * Source: 2026_02_22_000003_create_supervisions_table
 *
 * NOTE:
 * - supervisor_id FK ke `lecturers` (bukan users seperti CTMS asli)
 * - assigned_by tetap FK ke `users` karena admin bisa user umum
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_supervisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('lecturers')->cascadeOnDelete();
            $table->string('role'); // SUPERVISOR_1, SUPERVISOR_2
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['group_id', 'role']);   // no two SUPERVISOR_1 per group
            $table->index('supervisor_id');         // load calculation queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_supervisions');
    }
};
