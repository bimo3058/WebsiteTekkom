<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_thread_polls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('thread_id');
            $table->timestamp('expires_at')->nullable()->comment('null = tidak ada batas waktu');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->foreign('thread_id')->references('id')->on('mk_threads')->onDelete('cascade');
        });

        Schema::create('mk_thread_poll_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('poll_id');
            $table->string('text', 255);
            $table->unsignedInteger('votes_count')->default(0)->comment('cached count');
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('mk_thread_polls')->onDelete('cascade');
        });

        Schema::create('mk_thread_poll_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('poll_id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['poll_id', 'user_id'], 'poll_one_vote_per_user');
            $table->foreign('poll_id')->references('id')->on('mk_thread_polls')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('mk_thread_poll_options')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_thread_poll_votes');
        Schema::dropIfExists('mk_thread_poll_options');
        Schema::dropIfExists('mk_thread_polls');
    }
};
