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
        Schema::create('capstone_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('period_id')
                ->constrained('capstone_periods')
                ->onDelete('cascade');

            $table->foreignId('title_id')
                ->nullable()
                ->constrained('capstone_titles')
                ->nullOnDelete();

            $table->enum('status', [
                'BIDDING',
                'KELOMPOK_FINAL',
                'PDC1_ACTIVE',
                'SEM_PRO',
                'REVISI_PDC1',
                'PDC2_ACTIVE',
                'PDC2_COMPLETED',
                'EXPO_ELIGIBLE',
                'EXPO_DONE',
                'CLOSED'
            ])->default('BIDDING');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_groups');
    }
};
