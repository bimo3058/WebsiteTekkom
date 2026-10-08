<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: these tables are already created by earlier migrations
        // (2026_05_05_000022/000023, 2026_08_08_000000). Only create when missing.
        if (! Schema::hasTable('capstone_assessment_component_templates')) {
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
        }

        if (! Schema::hasTable('capstone_assessment_components')) {
            Schema::create('capstone_assessment_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
                $table->string('type');
                $table->string('code');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('weight', 5, 2);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['period_id', 'type', 'code']);
            });
        }

        if (! Schema::hasTable('capstone_assessment_scores')) {
            Schema::create('capstone_assessment_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('component_id')->constrained('capstone_assessment_components')->cascadeOnDelete();
                $table->foreignId('evaluator_id')->constrained('lecturers')->cascadeOnDelete();
                $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->decimal('score', 5, 2);
                $table->text('notes')->nullable();
                $table->string('evaluation_type');
                $table->timestamps();
                $table->foreignId('period_component_id')
                    ->nullable()
                    ->constrained('capstone_period_assessment_components')
                    ->nullOnDelete();

                $table->unique(['component_id', 'evaluator_id', 'student_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_assessment_scores');
        Schema::dropIfExists('capstone_assessment_components');
        Schema::dropIfExists('capstone_assessment_component_templates');
    }
};
