<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_login', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pengguna_id');
            $table->uuid('role_aktif_id');
            $table->string('token', 500)->unique();
            $table->timestamp('login_pada')->useCurrent();
            $table->timestamp('logout_pada')->nullable();
            $table->timestamp('kedaluwarsa_pada')->nullable();

            $table->foreign('pengguna_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->foreign('role_aktif_id')->references('id')->on('role')->onDelete('cascade');

            $table->index(['pengguna_id', 'login_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_login');
    }
};
