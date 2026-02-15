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
        Schema::create('capstone_group_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('schedule_id')
                ->constrained('capstone_group_schedules')
                ->onDelete('cascade');

            $table->foreignId('lecturer_id')
                ->constrained('lecturers')
                ->onDelete('cascade');

            $table->enum('role', ['SUPERVISOR','EXAMINER','TITLE_OWNER']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_group_evaluations');
    }
};
