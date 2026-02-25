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
        Schema::create('mk_prestasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kemahasiswaan_id')
                ->constrained('mk_kemahasiswaan')
                ->cascadeOnDelete();

            $table->string('nama_prestasi', 150);
            $table->string('tingkat', 50);
            $table->integer('tahun');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mk_prestasi');
    }
};
