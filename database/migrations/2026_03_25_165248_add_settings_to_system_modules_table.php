<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_modules', function (Blueprint $table) {
            $table->boolean('is_maintenance')->default(false)->after('is_active');
            $table->json('settings')->nullable()->after('is_maintenance');
        });
    }

    public function down(): void
    {
        Schema::table('system_modules', function (Blueprint $table) {
            $table->dropColumn(['is_maintenance', 'settings']);
        });
    }
};