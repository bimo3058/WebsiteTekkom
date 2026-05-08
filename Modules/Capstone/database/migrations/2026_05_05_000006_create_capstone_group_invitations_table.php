<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Group Invitations (undangan mahasiswa ke group)
 *
 * Source: 2026_02_26_103829_create_group_invitations_table
 *
 * NOTE:
 * - student_id, inviter_id FK ke `students` (bukan users seperti CTMS asli)
 *   karena yang ngundang & diundang sama-sama mahasiswa
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_group_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('students')->cascadeOnDelete();
            $table->string('status')->default('PENDING'); // PENDING, ACCEPTED, REJECTED
            $table->timestamps();

            // Hanya 1 undangan aktif per group per mahasiswa
            $table->unique(['group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_group_invitations');
    }
};
