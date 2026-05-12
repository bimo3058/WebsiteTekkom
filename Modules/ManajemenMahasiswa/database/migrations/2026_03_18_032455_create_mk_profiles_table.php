<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_dosen', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nip', 30)->unique();
            $table->string('program_studi', 100);
            $table->string('bidang_keahlian', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_mahasiswa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nim', 20)->unique();
            $table->integer('angkatan')->comment('tahun masuk');
            $table->string('program_studi', 100);
            $table->string('status', 20)->comment('enum: aktif, cuti, do, lulus');
            $table->unsignedBigInteger('dosen_wali_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dosen_wali_id')->references('id')->on('mk_dosen')->onDelete('set null');
        });

        Schema::create('mk_alumni', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nim', 20);
            $table->integer('angkatan');
            $table->string('program_studi', 100);
            $table->integer('tahun_lulus');
            $table->string('perusahaan', 255)->nullable();
            $table->string('jabatan', 255)->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_pengurus_himpunan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('organisasi', 255)->comment('nama himpunan');
            $table->string('jabatan_organisasi', 100);
            $table->string('periode', 20)->comment('misal 2024/2025');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_pengurus_himpunan');
        Schema::dropIfExists('mk_alumni');
        Schema::dropIfExists('mk_mahasiswa');
        Schema::dropIfExists('mk_dosen');
    }
};