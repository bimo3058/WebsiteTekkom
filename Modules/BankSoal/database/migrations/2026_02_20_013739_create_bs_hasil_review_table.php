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
        Schema::create('bs_hasil_review_rps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_detail_id');
            $table->unsignedBigInteger('parameter_id');
            $table->integer('skor');
            $table->timestamps();

            $table->foreign('rps_detail_id')
                ->references('id')->on('bs_rps_detail');

            $table->foreign('parameter_id')
                ->references('id')->on('bs_parameter')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_hasil_review');
    }
};
