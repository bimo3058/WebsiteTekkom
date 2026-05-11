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
        Schema::table('bs_penarikan_soal', function (Blueprint $table) {
            $table->enum('metode_ujian', ['online', 'offline'])->default('online')->after('tipe_ujian');
            $table->enum('status_cetak', ['pending', 'diproses', 'selesai', 'batal'])->nullable()->after('metode_ujian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_penarikan_soal', function (Blueprint $table) {
            $table->dropColumn(['metode_ujian', 'status_cetak']);
        });
    }
};
