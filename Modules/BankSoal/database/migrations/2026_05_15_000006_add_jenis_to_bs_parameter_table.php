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
        Schema::table('bs_parameter', function (Blueprint $table) {
            if (!Schema::hasColumn('bs_parameter', 'jenis')) {
                $table->string('jenis', 50)->default('rps')->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_parameter', function (Blueprint $table) {
            if (Schema::hasColumn('bs_parameter', 'jenis')) {
                $table->dropColumn('jenis');
            }
        });
    }
};
