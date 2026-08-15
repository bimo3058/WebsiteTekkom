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
        Schema::create('eo_mr_ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('lokasi');
            $table->integer('lantai')->nullable();
            $table->integer('kapasitas')->default(0);
            $table->json('fasilitas')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_mr_ruangans');
    }
};
