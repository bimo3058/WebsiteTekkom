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
        Schema::create('bs_cheat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kompre_session_id')->constrained('bs_kompre_session')->onDelete('cascade');
            $table->string('event_type'); // e.g., 'tab_switch', 'window_blur'
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_cheat_logs');
    }
};
