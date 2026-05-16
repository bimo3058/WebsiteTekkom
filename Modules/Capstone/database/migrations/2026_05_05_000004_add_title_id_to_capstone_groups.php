<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Add title_id FK ke capstone_groups
 *
 * Migration terpisah karena ada circular dependency:
 * - capstone_groups.title_id → capstone_titles.id
 * - capstone_titles.proposed_by_group_id → capstone_groups.id
 *
 * Solusi: bikin keduanya tanpa FK silang dulu, baru tambah salah satu di sini.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->foreignId('title_id')->nullable()
                ->constrained('capstone_titles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('title_id');
        });
    }
};
