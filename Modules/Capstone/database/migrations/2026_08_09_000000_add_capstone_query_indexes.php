<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capstone_periods', function (Blueprint $table) {
            $table->index(['is_active', 'deleted_at'], 'cap_period_active_idx');
        });

        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->index(['period_id', 'status'], 'cap_group_period_status_idx');
            $table->index(['title_id', 'status'], 'cap_group_title_status_idx');
            $table->index(['supervisor_1_id', 'period_id'], 'cap_group_supervisor1_period_idx');
            $table->index(['supervisor_2_id', 'period_id'], 'cap_group_supervisor2_period_idx');
        });

        Schema::table('capstone_titles', function (Blueprint $table) {
            $table->index(['lecturer_id', 'status'], 'cap_title_lecturer_status_idx');
            $table->index(
                ['proposed_supervisor_id', 'supervisor_approval_status'],
                'cap_title_proposed_supervisor_status_idx'
            );
            $table->index(
                ['proposed_by_group_id', 'supervisor_approval_status'],
                'cap_title_proposed_group_status_idx'
            );
            $table->index(['period_id', 'status'], 'cap_title_period_status_idx');
        });

        Schema::table('capstone_group_members', function (Blueprint $table) {
            $table->index(['group_id', 'student_id'], 'cap_group_member_group_student_idx');
        });

        Schema::table('capstone_documents', function (Blueprint $table) {
            $table->index(['group_id', 'phase', 'status'], 'cap_document_group_phase_status_idx');
            $table->index(['student_id', 'status'], 'cap_document_student_status_idx');
        });

        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            $table->index(['examiner_1_id', 'status', 'date'], 'cap_seminar_examiner1_status_date_idx');
            $table->index(['examiner_2_id', 'status', 'date'], 'cap_seminar_examiner2_status_date_idx');
        });

        Schema::table('capstone_ta_defense_schedules', function (Blueprint $table) {
            $table->index(['group_id', 'status', 'date'], 'cap_ta_defense_group_status_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('capstone_ta_defense_schedules', function (Blueprint $table) {
            $table->dropIndex('cap_ta_defense_group_status_date_idx');
        });

        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            $table->dropIndex('cap_seminar_examiner1_status_date_idx');
            $table->dropIndex('cap_seminar_examiner2_status_date_idx');
        });

        Schema::table('capstone_documents', function (Blueprint $table) {
            $table->dropIndex('cap_document_group_phase_status_idx');
            $table->dropIndex('cap_document_student_status_idx');
        });

        Schema::table('capstone_group_members', function (Blueprint $table) {
            $table->dropIndex('cap_group_member_group_student_idx');
        });

        Schema::table('capstone_titles', function (Blueprint $table) {
            $table->dropIndex('cap_title_lecturer_status_idx');
            $table->dropIndex('cap_title_proposed_supervisor_status_idx');
            $table->dropIndex('cap_title_proposed_group_status_idx');
            $table->dropIndex('cap_title_period_status_idx');
        });

        Schema::table('capstone_groups', function (Blueprint $table) {
            $table->dropIndex('cap_group_period_status_idx');
            $table->dropIndex('cap_group_title_status_idx');
            $table->dropIndex('cap_group_supervisor1_period_idx');
            $table->dropIndex('cap_group_supervisor2_period_idx');
        });

        Schema::table('capstone_periods', function (Blueprint $table) {
            $table->dropIndex('cap_period_active_idx');
        });
    }
};
