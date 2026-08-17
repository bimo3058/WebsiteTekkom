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
        Schema::table('eo_kp_penilaian', function (Blueprint $table) {
            $table->dropColumn('nilai_laporan_pembimbing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_penilaian', function (Blueprint $table) {
            $table->double('nilai_laporan_pembimbing')->nullable();
        });
    }
};
