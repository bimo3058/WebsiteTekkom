<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function createScoreTable(string $tableName, string $actorColumn): void
    {
        Schema::create($tableName, function (Blueprint $table) use ($actorColumn, $tableName) {
            $short = str_replace(['capstone_', '_scores'], ['', ''], $tableName);

            $table->id();
            $table->foreignId('component_id')->nullable()->constrained('capstone_assessment_components')->cascadeOnDelete();
            $table->foreignId('period_component_id')->nullable()->constrained('capstone_period_assessment_components')->nullOnDelete();
            $table->foreignId($actorColumn)->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('notes')->nullable();
            $table->string('evaluation_type')->nullable();
            $table->timestamps();

            $table->unique(
                ['period_component_id', $actorColumn, 'student_id', 'group_id'],
                "cap_{$short}_upsert_unique"
            );
            $table->index(['group_id', $actorColumn], "cap_{$short}_group_actor_idx");
        });
    }

    public function up(): void
    {
        Schema::create('capstone_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('type')->default('physical');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('capstone_assessment_component_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('capstone_peer_review_indicator_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('capstone_period_assessment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('capstone_assessment_component_templates')->cascadeOnDelete();
            $table->string('type');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['period_id', 'type', 'template_id'], 'cap_period_component_unique');
        });

        Schema::create('capstone_period_peer_review_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('capstone_peer_review_indicator_templates')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['period_id', 'template_id'], 'cap_period_peer_indicator_unique');
        });

        Schema::create('capstone_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('students')->cascadeOnDelete();
            $table->string('status')->default('PENDING');
            $table->text('message')->nullable();
            $table->timestamps();
            $table->unique(['group_id', 'requester_id'], 'cap_join_request_unique');
        });

        Schema::create('capstone_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->default('INDUSTRY');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('capstone_stakeholder_title', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stakeholder_id')->constrained('capstone_stakeholders')->cascadeOnDelete();
            $table->foreignId('title_id')->constrained('capstone_titles')->cascadeOnDelete();
            $table->string('role')->default('ADVISOR');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['stakeholder_id', 'title_id'], 'cap_stakeholder_title_unique');
        });

        Schema::create('capstone_period_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->string('status')->default('ACTIVE');
            $table->timestamp('flagged_at')->nullable();
            $table->foreignId('flagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'period_id'], 'cap_period_registration_unique');
        });

        Schema::create('capstone_student_peer_review_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->boolean('has_completed_peer_review')->default(false);
            $table->string('ta_status')->default('TA_BLOCKED');
            $table->timestamps();
            $table->unique(['student_id', 'period_id'], 'cap_student_peer_status_unique');
            $table->index(['group_id', 'ta_status'], 'cap_student_peer_group_status_idx');
        });

        Schema::create('capstone_finalization_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['period_id', 'action'], 'cap_finalization_period_action_idx');
        });

        Schema::create('capstone_title_approval_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->constrained('capstone_titles')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('affected_group_id')->nullable()->constrained('capstone_groups')->nullOnDelete();
            $table->string('action')->default('APPROVE');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->index(['title_id', 'created_at'], 'cap_title_approval_history_idx');
        });

        Schema::create('capstone_title_deletion_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->constrained('capstone_titles')->cascadeOnDelete();
            $table->string('title_name');
            $table->foreignId('lecturer_id')->constrained('lecturers');
            $table->foreignId('period_id')->constrained('capstone_periods');
            $table->json('affected_groups');
            $table->foreignId('deleted_by')->constrained('users');
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('capstone_ta_defense_schedule_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('capstone_ta_defense_schedules')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['schedule_id', 'student_id'], 'cap_ta_schedule_student_unique');
        });

        Schema::create('capstone_expo_student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expo_registration_id')->constrained('capstone_expo_registrations')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('storage_location')->default('supabase');
            $table->string('original_name');
            $table->string('status')->default('SUBMITTED');
            $table->timestamps();
            $table->unique(['expo_registration_id', 'student_id'], 'cap_expo_student_document_unique');
        });

        foreach (['bimbingan_sempro', 'bimbingan_ta', 'expo', 'milestone', 'nilai_dosen'] as $type) {
            $this->createScoreTable("capstone_{$type}_scores", 'evaluator_id');
        }
        $this->createScoreTable('capstone_sempro_scores', 'examiner_id');
        $this->createScoreTable('capstone_sidang_ta_scores', 'examiner_id');
    }

    public function down(): void
    {
        foreach ([
            'capstone_sidang_ta_scores',
            'capstone_sempro_scores',
            'capstone_nilai_dosen_scores',
            'capstone_milestone_scores',
            'capstone_expo_scores',
            'capstone_bimbingan_ta_scores',
            'capstone_bimbingan_sempro_scores',
            'capstone_expo_student_documents',
            'capstone_ta_defense_schedule_student',
            'capstone_title_deletion_audits',
            'capstone_title_approval_audits',
            'capstone_finalization_audits',
            'capstone_student_peer_review_status',
            'capstone_period_registrations',
            'capstone_stakeholder_title',
            'capstone_stakeholders',
            'capstone_join_requests',
            'capstone_period_peer_review_indicators',
            'capstone_period_assessment_components',
            'capstone_peer_review_indicator_templates',
            'capstone_assessment_component_templates',
            'capstone_locations',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
