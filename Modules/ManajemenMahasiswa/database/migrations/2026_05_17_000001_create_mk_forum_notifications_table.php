<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_forum_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');        // siapa yang menerima notif
            $table->unsignedBigInteger('actor_id');        // siapa yang melakukan aksi
            $table->string('type', 50);                    // reply_comment, reply_thread, mention
            $table->unsignedBigInteger('thread_id');
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index('thread_id');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('thread_id')->references('id')->on('mk_threads')->cascadeOnDelete();
            $table->foreign('comment_id')->references('id')->on('mk_comments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_forum_notifications');
    }
};
