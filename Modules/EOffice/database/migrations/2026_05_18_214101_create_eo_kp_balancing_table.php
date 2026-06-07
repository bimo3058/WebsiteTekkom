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
        Schema::create('eo_kp_balancing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kp_id')->constrained('eo_kerja_praktik')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('eo_kp_mahasiswa')->onDelete('cascade');
            $table->foreignId('dosen_id')->nullable()->constrained('eo_kp_dosen')->onDelete('set null');
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_balancing');
    }
};
