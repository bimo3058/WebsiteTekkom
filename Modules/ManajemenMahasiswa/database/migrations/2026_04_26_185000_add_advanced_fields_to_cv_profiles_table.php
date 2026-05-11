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
        Schema::table('cv_profiles', function (Blueprint $table) {
            $table->string('cv_domisili')->nullable()->after('cv_whatsapp');
            $table->string('cv_portfolio')->nullable()->after('cv_domisili');
            $table->json('kegiatan_organisasi')->nullable()->after('pengalaman_kerja');
            $table->json('proyek')->nullable()->after('kegiatan_organisasi');
            $table->json('bahasa')->nullable()->after('sertifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cv_profiles', function (Blueprint $table) {
            //
        });
    }
};
