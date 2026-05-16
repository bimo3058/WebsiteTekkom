<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->unsignedBigInteger('matkul_id')->nullable()->after('kode');
            $table->foreign('matkul_id')->references('id')->on('eo_matkul_praktikum')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->dropForeign(['matkul_id']);
            $table->dropColumn('matkul_id');
        });
    }
};