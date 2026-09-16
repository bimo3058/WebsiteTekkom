<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::table('capstone_peer_reviews', function (Blueprint $table) {
            $table->foreignId('indicator_id')->nullable()->change();
            $table->unique(['group_id', 'reviewer_id', 'reviewee_id', 'period_indicator_id'], 'cap_peer_period_submission_unique');
        });
    }

    public function down(): void
    {
        if (DB::table('capstone_peer_reviews')->whereNull('indicator_id')->exists()) {
            throw new RuntimeException('Period peer review submissions exist; preserve them before rolling back.');
        }
        Schema::table('capstone_peer_reviews', function (Blueprint $table) {
            $table->dropUnique('cap_peer_period_submission_unique');
            $table->foreignId('indicator_id')->nullable(false)->change();
        });
    }
};
