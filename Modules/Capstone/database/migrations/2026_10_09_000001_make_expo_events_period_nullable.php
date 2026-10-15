<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('capstone_expo_events') || ! Schema::hasColumn('capstone_expo_events', 'period_id')) {
            return;
        }

        Schema::table('capstone_expo_events', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
        });

        Schema::table('capstone_expo_events', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->change();
            $table->foreign('period_id')->references('id')->on('capstone_periods')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('capstone_expo_events') || ! Schema::hasColumn('capstone_expo_events', 'period_id')) {
            return;
        }

        Schema::table('capstone_expo_events', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
        });

        Schema::table('capstone_expo_events', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable(false)->change();
            $table->foreign('period_id')->references('id')->on('capstone_periods')->cascadeOnDelete();
        });
    }
};
