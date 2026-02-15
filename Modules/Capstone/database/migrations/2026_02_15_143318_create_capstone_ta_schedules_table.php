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
        Schema::create('capstone_ta_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ta_id')
                ->constrained('capstone_individual_ta')
                ->onDelete('cascade');

            $table->enum('status', ['SCHEDULED','COMPLETED','CANCELLED'])
                ->default('SCHEDULED');

            $table->timestamp('scheduled_at')->nullable();
            $table->string('location')->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_schedules');
    }
};
