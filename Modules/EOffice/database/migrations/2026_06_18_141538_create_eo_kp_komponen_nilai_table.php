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
        Schema::create('eo_kp_komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('eo_kp_periode')->cascadeOnDelete();
            $table->string('nama_komponen');
            $table->integer('bobot')->default(0);
            $table->enum('role_penilai', ['dosen_pembimbing', 'koordinator'])->default('dosen_pembimbing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_komponen_nilai');
    }
};
