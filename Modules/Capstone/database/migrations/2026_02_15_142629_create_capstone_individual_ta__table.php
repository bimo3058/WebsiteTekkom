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
        Schema::create('capstone_individual_ta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('capstone_groups')
                ->onDelete('cascade');

            $table->foreignId('period_id')
                ->constrained('capstone_periods')
                ->onDelete('cascade');

            $table->foreignId('student_id')
                ->constrained('students')
                ->onDelete('cascade');

            $table->enum('status', [
                'TA_LOCKED','TA_DRAFT','TA_REVISED',
                'TA_READY','TA_REGISTERED','TA_DEFENDED'
            ])->default('TA_LOCKED');

            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->unique(['student_id','period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_individual_ta_');
    }
};
