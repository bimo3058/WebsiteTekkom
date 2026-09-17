<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_blind_review_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('bs_mata_kuliah')->cascadeOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('nama_round')->nullable();
            $table->unsignedTinyInteger('required_reviewers')->default(1);
            $table->boolean('is_blind')->default(true);
            $table->enum('status', ['pending', 'in_review', 'completed'])->default('pending');
            $table->timestamps();

            $table->index('mk_id');
            $table->index('created_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_blind_review_rounds');
    }
};
