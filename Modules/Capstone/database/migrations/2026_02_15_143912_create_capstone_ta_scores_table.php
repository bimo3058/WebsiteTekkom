<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('capstone_ta_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evaluation_id')
                ->constrained('capstone_ta_evaluations')
                ->onDelete('cascade');

            $table->foreignId('rubric_item_id')
                ->constrained('capstone_rubric_items')
                ->onDelete('cascade');

            $table->float('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_scores');
    }
};
