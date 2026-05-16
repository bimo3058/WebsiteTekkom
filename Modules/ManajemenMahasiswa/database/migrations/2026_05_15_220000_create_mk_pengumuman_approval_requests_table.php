<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_pengumuman_approval_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengumuman_id');
            $table->unsignedBigInteger('requester_id')->comment('staff yang mengajukan');
            $table->unsignedBigInteger('verifier_id')->comment('ketua yang diminta verifikasi');
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected, cancelled');
            $table->text('pesan_pengaju')->nullable()->comment('pesan dari staff ke verifikator');
            $table->text('catatan_verifikator')->nullable()->comment('komentar dari verifikator saat approve/reject');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('pengumuman_id')->references('id')->on('mk_pengumuman')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verifier_id')->references('id')->on('users')->onDelete('cascade');

            // Index for fast lookup
            $table->index(['verifier_id', 'status']);
            $table->index(['pengumuman_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_pengumuman_approval_requests');
    }
};
