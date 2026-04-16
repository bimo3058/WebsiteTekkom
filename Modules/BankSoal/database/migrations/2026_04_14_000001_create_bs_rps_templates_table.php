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
        Schema::create('bs_rps_templates', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename'); // Original nama file saat diupload
            $table->string('filename'); // Nama file di storage (after processing)
            $table->string('file_path'); // Path di Supabase
            $table->integer('version')->default(1); // Nomor versi
            $table->unsignedBigInteger('created_by'); // User ID yang upload
            $table->boolean('is_latest')->default('true'); // Flag untuk versi terbaru
            $table->text('keterangan')->nullable(); // Deskripsi/keterangan template
            $table->timestamps();
            
            // Foreign key
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Index
            $table->index('is_latest');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_rps_templates');
    }
};
