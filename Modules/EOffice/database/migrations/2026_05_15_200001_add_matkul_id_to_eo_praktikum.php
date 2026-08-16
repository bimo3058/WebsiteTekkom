<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('eo_praktikum', 'matkul_id')) {
            return;
        }

        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->unsignedBigInteger('matkul_id')->nullable()->after('kode');
            $table->foreign('matkul_id')->references('id')->on('eo_matkul_praktikum')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Kolom dapat dimiliki migration 2026_05_15_095211.
    }
};
