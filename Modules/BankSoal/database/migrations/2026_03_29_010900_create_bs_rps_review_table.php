<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_rps_review', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->unsignedBigInteger('gpm_user_id')->nullable();
            $table->enum('status_review', ['draft', 'diajukan', 'revisi', 'disetujui'])->default('diajukan');
            $table->text('catatan')->nullable();
            $table->integer('nilai_akhir')->default(0);
            $table->timestamps();
            
            $table->foreign('rps_id')->references('id')->on('bs_rps_detail')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_rps_review');
    }
};
