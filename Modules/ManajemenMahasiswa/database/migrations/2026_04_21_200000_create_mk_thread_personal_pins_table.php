<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_thread_personal_pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('thread_id');
            $table->timestamps();

            $table->unique(['user_id', 'thread_id'], 'mk_personal_pins_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('thread_id')->references('id')->on('mk_threads')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_thread_personal_pins');
    }
};
