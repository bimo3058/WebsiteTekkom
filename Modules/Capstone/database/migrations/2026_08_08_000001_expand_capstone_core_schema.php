<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capstone_periods', function (Blueprint $table) {
            $table->boolean('is_finalized')->default(false);
            $table->dateTime('bidding_reminder_at')->nullable();
            $table->dateTime('pdc1_reminder_at')->nullable();
            $table->dateTime('pdc2_reminder_at')->nullable();
            $table->dateTime('expo_reminder_at')->nullable();
            $table->dateTime('ta_reminder_at')->nullable();
            $table->integer('max_supervisor_load')->nullable();
            $table->boolean('allow_solo')->default(false);
            $table->boolean('require_all_students_grouped')->default(true);
            $table->json('grade_configuration')->nullable();
        });

        DB::table('capstone_periods')->update([
            'max_supervisor_load' => DB::raw('max_supervise_load'),
        ]);

        Schema::table('capstone_titles', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained('capstone_periods')->cascadeOnDelete();
            $table->foreignId('pre_assigned_group_id')->nullable()->constrained('capstone_groups')->nullOnDelete();
            $table->boolean('is_reserved')->default(false);
        });

        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->string('code')->nullable()->unique();
            $table->boolean('is_solo')->default(false);
            $table->boolean('has_active_proposal')->default(false);
            $table->string('readiness_status')->default('NOT_READY');
            $table->text('finalization_notes')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('capstone_group_members', function (Blueprint $table) {
            $table->string('status')->default('active');
            $table->foreignId('removed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('removal_reason')->nullable();
            $table->softDeletes();
        });

        Schema::table('capstone_documents', function (Blueprint $table) {
            $table->string('storage_location')->default('supabase');
        });

        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained('capstone_locations')->nullOnDelete();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->softDeletes();
        });

        Schema::table('capstone_ta_submissions', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained('capstone_periods')->cascadeOnDelete();
        });

        Schema::table('capstone_ta_defense_schedules', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained('capstone_periods')->cascadeOnDelete();
            $table->foreignId('examiner_1_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->foreignId('examiner_2_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('capstone_locations')->nullOnDelete();
            $table->timestamp('evaluation_deadline')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
        });

        Schema::table('capstone_ta_defense_evaluations', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
        });

        Schema::table('capstone_expo_events', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained('capstone_locations')->nullOnDelete();
        });

        Schema::table('capstone_assessment_scores', function (Blueprint $table) {
            $table->foreignId('period_component_id')->nullable()->constrained('capstone_period_assessment_components')->nullOnDelete();
        });

        Schema::table('capstone_peer_reviews', function (Blueprint $table) {
            $table->foreignId('period_indicator_id')->nullable()->constrained('capstone_period_peer_review_indicators')->nullOnDelete();
            $table->decimal('raw_score', 5, 2)->nullable();
            $table->boolean('is_final_submission')->default(false);
            $table->timestamp('submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('capstone_peer_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('period_indicator_id');
            $table->dropColumn(['raw_score', 'is_final_submission', 'submitted_at']);
        });

        Schema::table('capstone_assessment_scores', function (Blueprint $table) {
            $table->dropConstrainedForeignId('period_component_id');
        });

        Schema::table('capstone_expo_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });

        Schema::table('capstone_ta_defense_evaluations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
        });

        Schema::table('capstone_ta_defense_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropConstrainedForeignId('examiner_2_id');
            $table->dropConstrainedForeignId('examiner_1_id');
            $table->dropConstrainedForeignId('period_id');
            $table->dropColumn(['evaluation_deadline', 'notes', 'final_score']);
        });

        Schema::table('capstone_ta_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('period_id');
        });

        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn(['final_score', 'deleted_at']);
        });

        Schema::table('capstone_documents', function (Blueprint $table) {
            $table->dropColumn('storage_location');
        });

        Schema::table('capstone_group_members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('removed_by');
            $table->dropColumn(['status', 'removal_reason', 'deleted_at']);
        });

        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('finalized_by');
            $table->dropUnique(['code']);
            $table->dropColumn([
                'code', 'is_solo', 'has_active_proposal', 'readiness_status',
                'finalization_notes', 'finalized_at',
            ]);
        });

        Schema::table('capstone_titles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pre_assigned_group_id');
            $table->dropConstrainedForeignId('period_id');
            $table->dropColumn('is_reserved');
        });

        Schema::table('capstone_periods', function (Blueprint $table) {
            $table->dropColumn([
                'is_finalized', 'bidding_reminder_at', 'pdc1_reminder_at',
                'pdc2_reminder_at', 'expo_reminder_at', 'ta_reminder_at',
                'max_supervisor_load', 'allow_solo',
                'require_all_students_grouped', 'grade_configuration',
            ]);
        });
    }
};
