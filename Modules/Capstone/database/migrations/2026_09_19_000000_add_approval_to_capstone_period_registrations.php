<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capstone_period_registrations', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->after('flagged_by')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });

        // Normalize legacy statuses: instant registrations become APPROVED,
        // lowercase variants uppercased. PENDING/REJECTED preserved if present.
        DB::table('capstone_period_registrations')->whereIn('status', ['active', 'ACTIVE'])->update(['status' => 'APPROVED']);
        DB::table('capstone_period_registrations')->whereIn('status', ['flagged'])->update(['status' => 'FLAGGED']);
        DB::table('capstone_period_registrations')->whereIn('status', ['pending'])->update(['status' => 'PENDING']);
        DB::table('capstone_period_registrations')->whereIn('status', ['rejected'])->update(['status' => 'REJECTED']);
        DB::table('capstone_period_registrations')->whereIn('status', ['approved'])->update(['status' => 'APPROVED']);

        Schema::table('capstone_period_registrations', function (Blueprint $table) {
            $table->index(['period_id', 'status'], 'cap_period_reg_period_status_idx');
            $table->index(['status', 'created_at'], 'cap_period_reg_status_created_idx');
        });

        // New registrations are join requests: default to PENDING so no
        // legacy insert path silently grants access without admin approval.
        if (in_array(DB::getDriverName(), ['pgsql', 'mysql'], true)) {
            DB::statement("ALTER TABLE capstone_period_registrations ALTER COLUMN status SET DEFAULT 'PENDING'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['pgsql', 'mysql'], true)) {
            DB::statement("ALTER TABLE capstone_period_registrations ALTER COLUMN status SET DEFAULT 'ACTIVE'");
        }

        Schema::table('capstone_period_registrations', function (Blueprint $table) {
            $table->dropIndex('cap_period_reg_period_status_idx');
            $table->dropIndex('cap_period_reg_status_created_idx');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['rejection_reason', 'reviewed_at']);
        });

        DB::table('capstone_period_registrations')->where('status', 'APPROVED')->update(['status' => 'ACTIVE']);
        DB::table('capstone_period_registrations')->where('status', 'PENDING')->update(['status' => 'ACTIVE']);
        DB::table('capstone_period_registrations')->where('status', 'REJECTED')->update(['status' => 'ACTIVE']);
    }
};
