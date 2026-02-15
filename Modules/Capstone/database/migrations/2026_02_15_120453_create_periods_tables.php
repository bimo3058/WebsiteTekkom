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
        Schema::create('capstone_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->boolean('is_active')->default(false);
            $table->boolean('is_locked')->default(false);

            $table->integer('min_group_size');
            $table->integer('max_group_size');

            $table->date('bidding_start')->nullable();
            $table->date('bidding_end')->nullable();
            $table->date('pdc1_start')->nullable();
            $table->date('pdc1_end')->nullable();
            $table->date('pdc2_start')->nullable();
            $table->date('pdc2_end')->nullable();
            $table->date('ta_start')->nullable();
            $table->date('ta_end')->nullable();
            $table->date('expo_date')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
