<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Peer Reviews (penilaian antar anggota group)
 *
 * Source: 2026_02_27_000004_create_peer_reviews_table
 *
 * NOTE:
 * - reviewer_id, reviewee_id FK ke `students` (peer review = mahasiswa ke mahasiswa)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_peer_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('indicator_id')
                ->constrained('capstone_peer_review_indicators')->cascadeOnDelete();
            $table->decimal('score', 5, 2);  // 0-100
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'reviewer_id', 'reviewee_id', 'indicator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_peer_reviews');
    }
};
