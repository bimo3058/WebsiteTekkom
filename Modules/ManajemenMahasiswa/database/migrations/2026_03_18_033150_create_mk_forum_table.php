<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_forum_mahasiswa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_forum', 255);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('mk_threads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('kategori', 50)->comment('enum: loker_karir, tanya_tugas, info_skripsi, sharing_alumni, umum');
            $table->string('judul', 255);
            $table->text('konten')->comment('rich text');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->unsignedBigInteger('best_answer_id')->nullable();
            $table->integer('vote_count')->default(0)->comment('cached count');
            $table->integer('comment_count')->default(0)->comment('cached count');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_discussion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('forum_id');
            $table->unsignedBigInteger('user_id');
            $table->string('judul_discussion', 255);
            $table->text('isi_discussion');
            $table->string('status', 50)->comment('enum: aktif, ditutup');
            $table->timestamps();

            $table->foreign('forum_id')->references('id')->on('mk_forum_mahasiswa')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_comment_forum', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('discussion_id');
            $table->unsignedBigInteger('user_id');
            $table->text('isi_comment');
            $table->timestamps();

            $table->foreign('discussion_id')->references('id')->on('mk_discussion')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('thread_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('untuk nested replies');
            $table->text('konten');
            $table->boolean('is_best_answer')->default(false);
            $table->integer('vote_count')->default(0)->comment('cached');
            $table->timestamps();

            $table->foreign('thread_id')->references('id')->on('mk_threads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('mk_comments')->onDelete('cascade');
        });

        // FK best_answer_id ditambah setelah mk_comments dibuat
        Schema::table('mk_threads', function (Blueprint $table) {
            $table->foreign('best_answer_id')->references('id')->on('mk_comments')->onDelete('set null');
        });

        Schema::create('mk_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('voteable_type', 255)->comment('MkThread atau MkComment (polymorphic)');
            $table->unsignedBigInteger('voteable_id');
            $table->smallInteger('value')->comment('+1 (upvote) atau -1 (downvote)');
            $table->timestamps();

            $table->unique(['user_id', 'voteable_type', 'voteable_id'], 'mk_votes_unique_user_voteable');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_votes');

        Schema::table('mk_threads', function (Blueprint $table) {
            $table->dropForeign(['best_answer_id']);
        });

        Schema::dropIfExists('mk_comments');
        Schema::dropIfExists('mk_comment_forum');
        Schema::dropIfExists('mk_discussion');
        Schema::dropIfExists('mk_threads');
        Schema::dropIfExists('mk_forum_mahasiswa');
    }
};