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
        Schema::create('capstone_group_workflow_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('capstone_groups')
                ->onDelete('cascade');

            $table->enum('old_status', [
                'BIDDING','KELOMPOK_FINAL','PDC1_ACTIVE','SEM_PRO',
                'REVISI_PDC1','PDC2_ACTIVE','PDC2_COMPLETED',
                'EXPO_ELIGIBLE','EXPO_DONE','CLOSED'
            ]);

            $table->enum('new_status', [
                'BIDDING','KELOMPOK_FINAL','PDC1_ACTIVE','SEM_PRO',
                'REVISI_PDC1','PDC2_ACTIVE','PDC2_COMPLETED',
                'EXPO_ELIGIBLE','EXPO_DONE','CLOSED'
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
        Schema::dropIfExists('capstone_group_workflow_logs');
    }
};
