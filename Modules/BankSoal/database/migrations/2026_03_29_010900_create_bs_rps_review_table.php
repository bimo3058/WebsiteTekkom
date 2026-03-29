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
            $table->binary('dokumen');
            $table->string('status_rps')->default('draft');
            $table->text('catatan')->nullable();
            $table->integer('nilai_akhir');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_rps_review');
    }
};
