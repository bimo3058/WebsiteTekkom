<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('nim_nip')->nullable()->unique();
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps(); // dibuat_pada, diperbarui_pada
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
