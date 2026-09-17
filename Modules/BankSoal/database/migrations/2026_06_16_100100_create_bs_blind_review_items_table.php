<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_blind_review_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained('bs_blind_review_rounds')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('bs_pertanyaan')->cascadeOnDelete();
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->boolean('is_blind')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['reviewer_id', 'status']);
            $table->index('pertanyaan_id');
            $table->index('round_id');
            $table->unique(['round_id', 'pertanyaan_id', 'reviewer_id'], 'bs_blind_review_items_unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_blind_review_items');
    }
};
