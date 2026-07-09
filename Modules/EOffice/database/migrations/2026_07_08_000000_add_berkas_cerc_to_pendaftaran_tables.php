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
        Schema::table('pendaftaran_asprak', function (Blueprint $table) {
            $table->string('berkas_cerc_path')->nullable()->after('cv_path');
        });

        Schema::table('pendaftaran_koordinator', function (Blueprint $table) {
            $table->string('berkas_cerc_path')->nullable()->after('transkrip_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_asprak', function (Blueprint $table) {
            $table->dropColumn('berkas_cerc_path');
        });

        Schema::table('pendaftaran_koordinator', function (Blueprint $table) {
            $table->dropColumn('berkas_cerc_path');
        });
    }
};
