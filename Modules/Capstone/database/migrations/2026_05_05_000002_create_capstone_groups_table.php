<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Groups
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_16_140420_create_groups_table
 * - 2026_02_21_070001_add_assignment_type_to_groups_table
 * - 2026_02_22_000007_update_groups_table_add_supervisor_cache
 * - 2026_02_27_000008_add_group_mode_to_groups_table
 *
 * NOTE:
 * - title_id ditambah belakangan setelah capstone_titles tabel dibuat
 *   (lihat migration add_title_id_to_capstone_groups)
 * - supervisor_*_id FK ke `lecturers` (bukan users seperti CTMS asli)
 *   karena webtekkom punya tabel lecturers terpisah.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();

            // title_id akan ditambah belakangan via separate migration
            // karena capstone_titles belum dibuat di urutan ini

            $table->string('status')->default('FORMING');
            // FORMING, BIDDING, TITLE_SELECTED, PDC1_ACTIVE, SEM_PRO, REVISI_PDC1,
            // PDC2_ACTIVE, PDC2_COMPLETED, EXPO_ELIGIBLE, EXPO_DONE, CLOSED

            $table->string('assignment_type')->nullable();
            // BIDDING, DIRECT, STUDENT_PROPOSAL

            $table->string('group_mode')->default('GROUP');
            // GROUP = kelompok, INDIVIDUAL = capstone individu

            $table->boolean('has_existing_group')->default(false);

            $table->foreignId('supervisor_1_id')->nullable()
                ->constrained('lecturers')->nullOnDelete();
            $table->foreignId('supervisor_2_id')->nullable()
                ->constrained('lecturers')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_groups');
    }
};
