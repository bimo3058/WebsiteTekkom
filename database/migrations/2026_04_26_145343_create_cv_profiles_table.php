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
        Schema::create('cv_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->text('tentang_diri')->nullable();           // Deskripsi singkat diri
            $table->json('pendidikan')->nullable();              // Array: [{institusi, jurusan, tahun_masuk, tahun_lulus}]
            $table->json('pengalaman_kerja')->nullable();        // Array: [{perusahaan, posisi, tahun_mulai, tahun_selesai, deskripsi}]
            $table->json('keahlian')->nullable();                // Array: [{nama, level}] — level: beginner/intermediate/advanced
            $table->json('sertifikasi')->nullable();             // Array: [{nama, penerbit, tahun}]
            $table->string('template', 30)->default('modern');   // Template CV yang dipilih
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_profiles');
    }
};
