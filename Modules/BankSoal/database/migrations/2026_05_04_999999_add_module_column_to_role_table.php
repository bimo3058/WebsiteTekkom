<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('role') && !Schema::hasColumn('role', 'module')) {
            Schema::table('role', function (Blueprint $table) {
                // Menambahkan kolom module yang diminta oleh RoleSeeder
                $table->string('module')->nullable()->after('nama');
            });
        }
    }

    public function down(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->dropColumn('module');
        });
    }
};