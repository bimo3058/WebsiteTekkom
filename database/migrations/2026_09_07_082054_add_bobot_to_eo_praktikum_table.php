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
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->integer('bobot_tp')->default(10)->after('jumlah_shift');
            $table->integer('bobot_praktikum')->default(30)->after('bobot_tp');
            $table->integer('bobot_laporan')->default(30)->after('bobot_praktikum');
            $table->integer('bobot_responsi')->default(30)->after('bobot_laporan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            //
        });
    }
};
