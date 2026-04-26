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
        Schema::table('bs_kompre_jawaban', function (Blueprint $table) {
            $table->unsignedBigInteger('jawaban_dipilih')->nullable()->change();
            
            // Drop enum columns and re-add them as nullable strings/booleans
            $table->dropColumn('kesulitan_now');
            $table->dropColumn('is_benar_now');
        });

        Schema::table('bs_kompre_jawaban', function (Blueprint $table) {
            $table->string('kesulitan_now')->nullable();
            $table->boolean('is_benar_now')->nullable();
            $table->json('urutan_opsi')->nullable()->after('urutan_soal');
        });
    }

    public function down(): void
    {
        Schema::table('bs_kompre_jawaban', function (Blueprint $table) {
            $table->dropColumn(['kesulitan_now', 'is_benar_now', 'urutan_opsi']);
        });
    }
};
