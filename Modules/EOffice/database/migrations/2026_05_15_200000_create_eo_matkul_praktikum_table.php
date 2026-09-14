<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eo_matkul_praktikum')) {
            return;
        }

        Schema::create('eo_matkul_praktikum', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode')->unique();
            $table->string('nama');
            $table->integer('sks');
            $table->unsignedBigInteger('semester')->nullable();
            $table->timestamps();  // created_at & updated_at, default CURRENT_TIMESTAMP
        });
    }

    public function down(): void
    {
        // Tabel dapat dimiliki migration 2026_05_15_095100.
    }
};
