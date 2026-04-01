<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('filename');
            $table->string('path'); // WAJIB: Untuk tahu file mana yang dibaca di Bucket
            $table->string('file_hash')->index(); // WAJIB: Untuk cek apakah file sudah pernah diupload
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('error_message')->nullable(); // Opsional: Untuk mencatat kalau ada baris yang gagal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_statuses');
    }
};