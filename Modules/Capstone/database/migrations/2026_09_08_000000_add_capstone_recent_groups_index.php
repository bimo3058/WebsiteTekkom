<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->index(['created_at', 'id'], 'cap_group_recent_idx');
        });
    }

    public function down(): void
    {
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->dropIndex('cap_group_recent_idx');
        });
    }
};
