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
        Schema::create('capstone_group_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('capstone_groups')
                ->onDelete('cascade');

            $table->enum('type', ['SEMPRO','EXPO']);
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
        Schema::dropIfExists('capstone_group_schedules_');
    }
};
