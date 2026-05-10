<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Group Members
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_16_140425_create_group_members_table
 * - 2026_02_16_144321_add_is_leader_to_group_members_table
 * - 2026_02_24_000006_add_period_id_to_group_members
 *
 * NOTE:
 * - student_id FK ke `students` (bukan users seperti CTMS asli)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->boolean('is_leader')->default(false);
            $table->timestamps();

            // DB-level unique: 1 mahasiswa hanya bisa ikut 1 group dalam 1 period
            $table->unique(['student_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_group_members');
    }
};
