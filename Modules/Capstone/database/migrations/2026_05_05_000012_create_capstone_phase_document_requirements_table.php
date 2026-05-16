<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Phase Document Requirements
 * (master kebutuhan dokumen per fase per period)
 *
 * Source: 2026_02_24_000004_create_phase_document_requirements_table
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_phase_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->string('phase');           // PDC1, PDC2, TA
            $table->string('name');            // e.g. Proposal, Logbook, Final Report
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_phase_document_requirements');
    }
};
