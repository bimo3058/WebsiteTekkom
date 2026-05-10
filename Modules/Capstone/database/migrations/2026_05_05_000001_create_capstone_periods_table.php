<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Periods (master period TA / capstone)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_16_140411_create_periods_table
 * - 2026_02_16_153246_add_is_active_to_periods_table
 * - 2026_02_22_000006_update_periods_table_add_config_fields
 * - 2026_02_24_000005_add_soft_deletes_to_periods
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('phase_dates')->nullable();
            $table->boolean('is_active')->default(true);

            // Phase configuration
            $table->dateTime('bidding_start')->nullable();
            $table->dateTime('bidding_end')->nullable();
            $table->timestamp('bidding_locked_at')->nullable();
            $table->date('pdc1_start')->nullable();
            $table->date('pdc1_end')->nullable();
            $table->date('pdc2_start')->nullable();
            $table->date('pdc2_end')->nullable();
            $table->date('expo_date')->nullable();
            $table->date('ta_start')->nullable();
            $table->date('ta_end')->nullable();

            // Group & supervision config
            $table->integer('min_group_size')->default(2);
            $table->integer('max_group_size')->default(4);
            $table->integer('max_supervise_load')->default(8);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_periods');
    }
};
