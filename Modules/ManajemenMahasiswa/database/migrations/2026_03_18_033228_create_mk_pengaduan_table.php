<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_pengaduan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('pelapor');
            $table->string('kategori', 50)->comment('enum: akademik, pembelajaran, tendik, tugas_beban, fasilitas, lainnya');
            $table->boolean('is_anonim')->default(false);
            $table->jsonb('data_template')->nullable()->comment('field dinamis sesuai kategori');
            $table->string('status', 50)->default('baru')->comment('enum: baru, dibaca');
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('read_by')->nullable()->comment('siapa yang membaca');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('read_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('mk_xp_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('action', 50)->comment('enum: create_thread, comment, receive_upvote, best_answer, daily_login, streak_bonus');
            $table->integer('xp_amount');
            $table->string('reference_type', 255)->nullable()->comment('MkThread/MkComment (polymorphic)');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_badges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->comment('Thread Starter, Hot Topic, dll');
            $table->string('slug', 100)->unique();
            $table->string('icon', 10)->comment('emoji');
            $table->text('description');
            $table->string('criteria_type', 50)->comment('enum: thread_count, upvote_count, best_answer_count, streak, total_xp');
            $table->integer('criteria_value')->comment('threshold untuk unlock');
            $table->timestamps();
        });

        Schema::create('mk_user_badges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('badge_id');
            $table->timestamp('earned_at');

            $table->unique(['user_id', 'badge_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('badge_id')->references('id')->on('mk_badges')->onDelete('cascade');
        });

        Schema::create('mk_streaks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique()->comment('1 record per user');
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mk_dashboard_analitik', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->unsignedBigInteger('generated_by_user_id')->nullable();
            $table->timestamp('tanggal_generate');
            $table->integer('total_mahasiswa_aktif');
            $table->integer('total_alumni');
            $table->integer('total_kegiatan');
            $table->integer('total_pengumuman');
            $table->timestamps();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('set null');
            $table->foreign('generated_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_dashboard_analitik');
        Schema::dropIfExists('mk_streaks');
        Schema::dropIfExists('mk_user_badges');
        Schema::dropIfExists('mk_badges');
        Schema::dropIfExists('mk_xp_logs');
        Schema::dropIfExists('mk_pengaduan');
    }
};