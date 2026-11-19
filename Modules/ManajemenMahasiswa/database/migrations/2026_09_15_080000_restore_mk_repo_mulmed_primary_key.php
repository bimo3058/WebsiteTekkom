<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Restored databases may be missing the primary key from the original migration.
        if (! Schema::hasIndex('mk_repo_mulmed', ['id'], 'primary')) {
            Schema::table('mk_repo_mulmed', function (Blueprint $table) {
                $table->primary('id');
            });
        }
    }

    public function down(): void
    {
        // Keep the original table's primary key: other tables depend on it.
    }
};
