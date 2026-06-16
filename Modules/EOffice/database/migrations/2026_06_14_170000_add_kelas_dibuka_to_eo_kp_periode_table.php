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
        Schema::table('eo_kp_periode', function (Blueprint $table) {
            if (!Schema::hasColumn('eo_kp_periode', 'kelas_dibuka')) {
                $table->text('kelas_dibuka')->nullable()->after('pasca_kp_selesai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_periode', function (Blueprint $table) {
            if (Schema::hasColumn('eo_kp_periode', 'kelas_dibuka')) {
                $table->dropColumn('kelas_dibuka');
            }
        });
    }
};
