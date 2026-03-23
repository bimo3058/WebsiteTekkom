<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_pengumuman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('pembuat');
            $table->string('judul', 255);
            $table->text('konten')->comment('rich text (HTML)');
            $table->string('target_audience', 50)->comment('enum: semua, mahasiswa, alumni, dosen');
            $table->string('status_publish', 50)->comment('enum: draft, published, scheduled');
            $table->boolean('is_draft')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_pengumuman_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengumuman_id');
            $table->string('file_path', 255);
            $table->string('file_name', 255)->comment('nama asli file');
            $table->integer('file_size')->comment('dalam bytes');
            $table->timestamps();

            $table->foreign('pengumuman_id')->references('id')->on('mk_pengumuman')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_pengumuman_attachments');
        Schema::dropIfExists('mk_pengumuman');
    }
};