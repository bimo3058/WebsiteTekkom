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
        Schema::create('capstone_ta_workflow_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ta_id')
                ->constrained('capstone_individual_ta')
                ->onDelete('cascade');

            $table->enum('old_status', [
                'TA_LOCKED','TA_DRAFT','TA_REVISED',
                'TA_READY','TA_REGISTERED','TA_DEFENDED'
            ]);

            $table->enum('new_status', [
                'TA_LOCKED','TA_DRAFT','TA_REVISED',
                'TA_READY','TA_REGISTERED','TA_DEFENDED'
            ]);

            $table->foreignId('changed_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamp('changed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_workflow_logs');
    }
};
