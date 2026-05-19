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
        Schema::table('eo_kp_seminar', function (Blueprint $table) {
            $table->enum('status_validasi_dosen', ['pending', 'approved', 'rejected'])->default('pending')->after('status_validasi_syarat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_seminar', function (Blueprint $table) {
            $table->dropColumn('status_validasi_dosen');
        });
    }
};
