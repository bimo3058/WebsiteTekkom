<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_kemahasiswaan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nama', 255);
            $table->string('nim', 30);
            $table->integer('angkatan');
            $table->string('status', 50)->comment('enum: aktif, cuti, do, lulus');
            $table->integer('tahun_lulus')->nullable();
            $table->string('profesi', 255)->nullable();
            $table->string('kontak', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_prestasi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kemahasiswaan_id');
            $table->string('nama_prestasi', 255);
            $table->string('tingkat', 100)->comment('enum: lokal, nasional, internasional');
            $table->integer('tahun');
            $table->timestamps();

            $table->foreign('kemahasiswaan_id')->references('id')->on('mk_kemahasiswaan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_prestasi');
        Schema::dropIfExists('mk_kemahasiswaan');
    }
};