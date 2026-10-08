<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: member-flags columns missing from 90f2dc67.
 *
 * Commit 90f2dc67 ("group messaging and member flags") added SoftDeletes plus
 * status/removed_by/removal_reason to the GroupMember model and writes them in
 * StudentFlagService::flagStudentForPeriod(), but shipped no migration. Without
 * these columns every GroupMember query fails (the SoftDeletes global scope
 * references deleted_at) and flagging a student fails on update. This migration
 * supplies the missing columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: columns already added by 2026_08_08_000001. Only add when missing.
        Schema::table('capstone_group_members', function (Blueprint $table) {
            if (! Schema::hasColumn('capstone_group_members', 'status')) {
                $table->string('status')->nullable();
            }
            if (! Schema::hasColumn('capstone_group_members', 'removed_by')) {
                $table->unsignedBigInteger('removed_by')->nullable();
            }
            if (! Schema::hasColumn('capstone_group_members', 'removal_reason')) {
                $table->text('removal_reason')->nullable();
            }
            if (! Schema::hasColumn('capstone_group_members', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('capstone_group_members', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['status', 'removed_by', 'removal_reason']);
        });
    }
};
