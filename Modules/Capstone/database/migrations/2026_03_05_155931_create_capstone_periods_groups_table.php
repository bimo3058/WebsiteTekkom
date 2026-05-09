<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capstone_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('min_group_size')->default(3);
            $table->integer('max_group_size')->default(4);
            $table->integer('max_supervise_load')->default(5);
            $table->date('bidding_start')->nullable();
            $table->date('bidding_end')->nullable();
            $table->date('pdc1_start')->nullable();
            $table->date('pdc1_end')->nullable();
            $table->date('pdc2_start')->nullable();
            $table->date('pdc2_end')->nullable();
            $table->date('ta_start')->nullable();
            $table->date('ta_end')->nullable();
            $table->date('expo_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // groups dibuat dulu karena titles butuh FK ke groups (proposed_by_group_id)
        // Tapi titles juga di-FK dari groups.title_id → solusi: buat keduanya tanpa FK dulu, lalu tambah constraint
        Schema::create('capstone_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->unsignedBigInteger('title_id')->nullable();
            $table->string('status')->default('FORMING');
            // FORMING, BIDDING, TITLE_SELECTED, PDC1_ACTIVE, SEM_PRO, REVISI_PDC1,
            // PDC2_ACTIVE, PDC2_COMPLETED, EXPO_ELIGIBLE, EXPO_DONE, CLOSED
            $table->unsignedBigInteger('supervisor_1_id')->nullable();
            $table->unsignedBigInteger('supervisor_2_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('capstone_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('problem_statement')->nullable();
            $table->text('scope')->nullable();
            $table->json('specializations')->nullable();
            $table->integer('quota')->default(1);
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
            $table->boolean('approved_by_admin')->default(false);
            $table->string('title_source')->default('LECTURER'); // LECTURER, STUDENT
            $table->unsignedBigInteger('proposed_by_group_id')->nullable();
            $table->unsignedBigInteger('proposed_supervisor_id')->nullable();
            $table->string('supervisor_approval_status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proposed_by_group_id')->references('id')->on('capstone_groups')->nullOnDelete();
            $table->foreign('proposed_supervisor_id')->references('id')->on('lecturers')->nullOnDelete();
        });

        // Tambah FK setelah titles dibuat
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->foreign('title_id')->references('id')->on('capstone_titles')->nullOnDelete();
            $table->foreign('supervisor_1_id')->references('id')->on('lecturers')->nullOnDelete();
            $table->foreign('supervisor_2_id')->references('id')->on('lecturers')->nullOnDelete();
        });

        Schema::create('capstone_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->boolean('is_leader')->default(false);
            $table->timestamps();

            $table->unique(['group_id', 'student_id']);
        });

        Schema::create('capstone_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('title_id')->constrained('capstone_titles')->cascadeOnDelete();
            $table->integer('priority');
            $table->string('status')->default('PENDING'); // PENDING, ACCEPTED, REJECTED
            $table->string('lecturer_recommendation')->nullable(); // ACCEPT, REJECT
            $table->unsignedBigInteger('proposed_supervisor_1_id')->nullable();
            $table->unsignedBigInteger('proposed_supervisor_2_id')->nullable();
            $table->timestamps();

            $table->foreign('proposed_supervisor_1_id')->references('id')->on('lecturers')->nullOnDelete();
            $table->foreign('proposed_supervisor_2_id')->references('id')->on('lecturers')->nullOnDelete();
        });

        Schema::create('capstone_supervisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->string('role'); // SUPERVISOR_1, SUPERVISOR_2
            $table->timestamps();

            $table->unique(['group_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->dropForeign(['title_id']);
            $table->dropForeign(['supervisor_1_id']);
            $table->dropForeign(['supervisor_2_id']);
        });

        Schema::dropIfExists('capstone_supervisions');
        Schema::dropIfExists('capstone_bids');
        Schema::dropIfExists('capstone_group_members');
        Schema::dropIfExists('capstone_titles');
        Schema::dropIfExists('capstone_groups');
        Schema::dropIfExists('capstone_periods');
    }
};