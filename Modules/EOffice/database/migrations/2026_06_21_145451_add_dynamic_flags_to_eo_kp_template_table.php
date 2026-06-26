<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eo_kp_template', function (Blueprint $table) {
            $table->boolean('is_downloadable')->default(true)->after('is_required');
            $table->boolean('is_uploadable')->default(false)->after('is_downloadable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_template', function (Blueprint $table) {
            $table->dropColumn(['is_downloadable', 'is_uploadable']);
        });
    }
};
