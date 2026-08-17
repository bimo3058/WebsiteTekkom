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
            $table->dropColumn(['nilai_lapangan', 'nilai_seminar_pembimbing']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_penilaian', function (Blueprint $table) {
            $table->double('nilai_lapangan')->nullable();
            $table->double('nilai_seminar_pembimbing')->nullable();
        });
    }
};
