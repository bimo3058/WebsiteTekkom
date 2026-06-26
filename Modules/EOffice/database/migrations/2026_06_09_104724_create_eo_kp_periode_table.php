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
        Schema::create('eo_kp_periode', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran')->comment('Contoh: 2025/2026');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false)->comment('Hanya 1 periode yang boleh aktif');
            $table->date('tanggal_buka')->nullable()->comment('Tanggal buka pendaftaran KP');
            $table->date('tanggal_tutup')->nullable()->comment('Tanggal tutup pendaftaran KP');
            $table->timestamps();

            // Tidak boleh ada duplikat kombinasi tahun ajaran + semester
            $table->unique(['tahun_ajaran', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_periode');
    }
};
