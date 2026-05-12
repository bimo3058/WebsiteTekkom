<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_penarikan_soal_archived', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penarikan_id');
            $table->unsignedBigInteger('arsip_id');
            $table->timestamp('archived_at')->useCurrent();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->text('catatan_konversi')->nullable();
            $table->timestamps();

            $table->foreign('penarikan_id')->references('id')->on('bs_penarikan_soal')->cascadeOnDelete();
            $table->foreign('arsip_id')->references('id')->on('bs_arsip_soal')->cascadeOnDelete();
            $table->foreign('archived_by')->references('id')->on('users')->nullOnDelete();
            $table->index('penarikan_id');
            $table->index('arsip_id');
            $table->unique(['penarikan_id', 'arsip_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_penarikan_soal_archived');
    }
};