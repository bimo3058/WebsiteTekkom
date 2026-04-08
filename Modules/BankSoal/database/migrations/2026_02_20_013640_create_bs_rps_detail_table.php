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
        Schema::create('bs_rps_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mk_id');
            $table->string('semester');
            $table->string('tahun_ajaran');
            $table->string('dokumen');
            $table->enum('status', ['draft', 'diajukan', 'revisi', 'disetujui'])->default('diajukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_rps_detail');
    }
};
