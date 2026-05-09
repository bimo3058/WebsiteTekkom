<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Bids (bidding group ke title)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_22_000001_create_bids_table
 * - 2026_02_22_100001_add_proposed_supervisors_to_bids
 *
 * NOTE:
 * - proposed_supervisor_*_id FK ke `lecturers` (bukan users seperti CTMS asli)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('title_id')->constrained('capstone_titles')->cascadeOnDelete();
            $table->integer('priority');
            $table->string('status')->default('PENDING'); // PENDING, ACCEPTED, REJECTED
            $table->string('lecturer_recommendation')->nullable(); // ACCEPT, REJECT

            $table->foreignId('proposed_supervisor_1_id')->nullable()
                ->constrained('lecturers')->nullOnDelete();
            $table->foreignId('proposed_supervisor_2_id')->nullable()
                ->constrained('lecturers')->nullOnDelete();

            $table->timestamps();

            $table->unique(['group_id', 'priority']); // no duplicate priority per group
            $table->unique(['group_id', 'title_id']); // no duplicate bid to same title
            $table->index('title_id');                // title-centric queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_bids');
    }
};
