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
        Schema::create('capstone_rubric_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rubric_id')
                ->constrained('capstone_evaluation_rubrics')
                ->onDelete('cascade');

            $table->string('name');
            $table->float('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_rubric_items');
    }
};
