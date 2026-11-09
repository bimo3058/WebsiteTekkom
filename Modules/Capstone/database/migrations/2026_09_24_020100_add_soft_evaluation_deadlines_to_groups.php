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
        Schema::table('capstone_groups', function (Blueprint $table) {
            if (! Schema::hasColumn('capstone_groups', 'nilai_dosen_deadline')) {
                $table->timestamp('nilai_dosen_deadline')->nullable()->after('status');
            }

            if (! Schema::hasColumn('capstone_groups', 'milestone_deadline')) {
                $table->timestamp('milestone_deadline')->nullable()->after('nilai_dosen_deadline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->dropColumn(['nilai_dosen_deadline', 'milestone_deadline']);
        });
    }
};
