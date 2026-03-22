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
            $table->unsignedBigInteger('rps_id');
            $table->binary('dokumen');
            $table->enum('status_rps', ['disetujui','revisi','draft','diajukan'])->default('draft');
            $table->text('catatan')->nullable();
            $table->integer('nilai_akhir');
            $table->timestamps();

            $table->foreign('rps_id')
                ->references('id')->on('bs_rps')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
