<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->boolean('lulus')->nullable();
            $table->json('cpl_scores')->nullable();
            $table->index(['status', 'lulus'], 'idx_kompre_session_status_lulus');
        });
    }

    public function down(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->dropIndex('idx_kompre_session_status_lulus');
            $table->dropColumn(['lulus', 'cpl_scores']);
        });
    }
};
