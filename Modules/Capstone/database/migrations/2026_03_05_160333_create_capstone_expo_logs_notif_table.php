<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capstone_expo_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('capstone_periods')->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('capstone_expo_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expo_event_id')->constrained('capstone_expo_events')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->string('status')->default('REGISTERED'); // REGISTERED, CANCELLED
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->unique(['expo_event_id', 'group_id']);
        });

        // Group status transition log
        Schema::create('capstone_group_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->string('old_status');
            $table->string('new_status');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('capstone_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('capstone_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_notifications');
        Schema::dropIfExists('capstone_audit_logs');
        Schema::dropIfExists('capstone_group_workflow_logs');
        Schema::dropIfExists('capstone_expo_registrations');
        Schema::dropIfExists('capstone_expo_events');
    }
};