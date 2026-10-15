<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Group-level supervisor review of the EXPO submission (self-evaluation
     * scores + supporting document). One approval per group covers the whole
     * submission: APPROVED locks it and makes the score count towards PDC2,
     * REJECTED lets the representative member re-score and re-upload.
     */
    public function up(): void
    {
        if (! Schema::hasTable('capstone_expo_registrations')) {
            return;
        }

        Schema::table('capstone_expo_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('capstone_expo_registrations', 'review_status')) {
                $table->string('review_status')->nullable()->after('status');
            }
            if (! Schema::hasColumn('capstone_expo_registrations', 'review_feedback')) {
                $table->text('review_feedback')->nullable()->after('review_status');
            }
            if (! Schema::hasColumn('capstone_expo_registrations', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('review_feedback')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('capstone_expo_registrations', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        // Legacy submissions predate the review workflow: any registration
        // that already collected self-evaluations is treated as approved so
        // existing PDC2 grades keep counting.
        if (Schema::hasTable('capstone_expo_self_evaluations')) {
            DB::table('capstone_expo_registrations')
                ->whereNull('review_status')
                ->whereIn('id', function ($query) {
                    $query->select('expo_registration_id')
                        ->from('capstone_expo_self_evaluations')
                        ->groupBy('expo_registration_id');
                })
                ->update(['review_status' => 'APPROVED']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('capstone_expo_registrations') || ! Schema::hasColumn('capstone_expo_registrations', 'review_status')) {
            return;
        }

        Schema::table('capstone_expo_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['review_status', 'review_feedback', 'reviewed_at']);
        });
    }
};
