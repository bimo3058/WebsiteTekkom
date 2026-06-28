<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('eo_mr_jadwal_internal');

        Schema::create('eo_mr_jadwal_internal', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke Ruangan Master
            $table->foreignId('ruangan_id')->constrained('eo_mr_ruangans')->cascadeOnDelete();

            // Konfigurasi Bentuk Jadwal
            $table->string('tipe_jadwal')->default('spesifik');
            $table->string('kategori'); // Sidang, Rapat, Kuliah, Reparasi

            // Penanda Waktu (terpisah logika untuk Rutin & Spesifik)
            $table->tinyInteger('hari')->nullable(); // 1 (Senin) s/d 7 (Minggu)
            $table->date('tanggal_spesifik')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            // Log Keterangan Acara
            $table->string('keterangan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eo_mr_jadwal_internal');
    }
};
