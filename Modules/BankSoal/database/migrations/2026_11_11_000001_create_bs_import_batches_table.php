<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipe'); // legacy_rekap | template
            $table->string('nama_file');
            $table->string('file_hash', 64)->nullable()->index();
            $table->unsignedInteger('total_baris')->default(0);
            $table->unsignedInteger('berhasil')->default(0);
            $table->unsignedInteger('dilewati')->default(0);
            $table->unsignedInteger('gagal')->default(0);
            $table->json('errors')->nullable();
            $table->string('status')->default('preview'); // preview | committed | failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_import_batches');
    }
};
