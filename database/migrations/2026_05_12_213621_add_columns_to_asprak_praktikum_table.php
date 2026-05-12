<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika table belum ada, create
        if (!Schema::hasTable('asprak_praktikum')) {
            Schema::create('asprak_praktikum', function (Blueprint $table) {
                $table->id();
                $table->uuid('praktikum_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->enum('role', ['asprak', 'koor'])->default('asprak');
                $table->text('deskripsi')->nullable();
                $table->softDeletes();
                $table->timestamps();

                // Foreign keys
                $table->foreign('praktikum_id')
                    ->references('id')
                    ->on('eo_praktikum')
                    ->onDelete('cascade');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                // Unique: 1 user tidak bisa 2x asprak di praktikum sama
                $table->unique(['praktikum_id', 'user_id']);
            });
        } else {
            // Jika table sudah ada, tinggal add columns yang hilang
            Schema::table('asprak_praktikum', function (Blueprint $table) {
                if (!Schema::hasColumn('asprak_praktikum', 'praktikum_id')) {
                    $table->uuid('praktikum_id')->nullable();
                }
                if (!Schema::hasColumn('asprak_praktikum', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('asprak_praktikum', 'role')) {
                    $table->enum('role', ['asprak', 'koor'])->default('asprak');
                }
                if (!Schema::hasColumn('asprak_praktikum', 'deskripsi')) {
                    $table->text('deskripsi')->nullable();
                }
                if (!Schema::hasColumn('asprak_praktikum', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asprak_praktikum');
    }
};