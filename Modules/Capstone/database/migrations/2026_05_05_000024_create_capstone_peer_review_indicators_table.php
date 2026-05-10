<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Peer Review Indicators (master indikator peer review)
 *
 * Source: 2026_02_27_000003_create_peer_review_indicators_table
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_peer_review_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->string('name');            // e.g. "Kontribusi Teknis"
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2);   // bobot 0-100
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_peer_review_indicators');
    }
};
