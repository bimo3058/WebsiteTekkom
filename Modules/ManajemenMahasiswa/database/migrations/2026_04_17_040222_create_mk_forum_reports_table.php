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
        Schema::create('mk_forum_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('thread_id')->constrained('mk_threads')->onDelete('cascade');
            $table->text('alasan');
            $table->string('status', 50)->default('pending')->comment('pending, ditinjau, disetujui, ditolak');
            $table->timestamps();

            // Seorang user hanya bisa mereport sebuah thread satu kali
            $table->unique(['user_id', 'thread_id'], 'mk_forum_reports_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mk_forum_reports');
    }
};
