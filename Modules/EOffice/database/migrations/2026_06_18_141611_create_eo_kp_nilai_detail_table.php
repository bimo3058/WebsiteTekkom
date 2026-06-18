<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eo_kp_nilai_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kp_id')->constrained('eo_kerja_praktik')->cascadeOnDelete();
            $table->foreignId('komponen_id')->constrained('eo_kp_komponen_nilai')->cascadeOnDelete();
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_nilai_detail');
    }
};
