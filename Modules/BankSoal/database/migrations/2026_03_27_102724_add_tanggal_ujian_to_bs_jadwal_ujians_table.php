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
        Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
            $table->date('tanggal_ujian')->nullable()->after('nama_sesi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
            $table->dropColumn('tanggal_ujian');
        });
    }
};
