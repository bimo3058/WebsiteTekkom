<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Expo Events (master expo event yg dibuat admin)
 *
 * Source: 2026_02_24_000001_create_expo_events_table
 *
 * NOTE:
 * - created_by tetap FK ke `users` (bisa admin/staff)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_expo_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room');
            $table->integer('capacity');
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_expo_events');
    }
};
