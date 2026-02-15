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
        Schema::create('capstone_titles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('period_id')
                ->constrained('capstone_periods')
                ->onDelete('cascade');

            $table->foreignId('lecturer_id')
                ->constrained('lecturers')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('quota');
            $table->boolean('is_open')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
