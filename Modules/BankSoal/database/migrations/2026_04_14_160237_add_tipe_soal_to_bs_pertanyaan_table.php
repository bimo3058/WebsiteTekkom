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
        Schema::table('bs_pertanyaan', function (Blueprint $table) {
            $table->string('tipe_soal')->default('pilihan_ganda')->after('status')->comment('pilihan_ganda, essay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_pertanyaan', function (Blueprint $table) {
            $table->dropColumn('tipe_soal');
        });
    }
};
