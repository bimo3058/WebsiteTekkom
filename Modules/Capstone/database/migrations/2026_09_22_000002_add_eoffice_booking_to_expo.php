<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('capstone_expo_events') && ! Schema::hasColumn('capstone_expo_events', 'eoffice_peminjaman_id')) {
            Schema::table('capstone_expo_events', function (Blueprint $t) {
                // No FK: eo_mr_peminjamans.id has no unique constraint (same as seminar/ta_defense links).
                $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable()->after('eoffice_ruangan_id');
                $t->index('eoffice_peminjaman_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('capstone_expo_events') && Schema::hasColumn('capstone_expo_events', 'eoffice_peminjaman_id')) {
            Schema::table('capstone_expo_events', function (Blueprint $t) {
                $t->dropIndex(['eoffice_peminjaman_id']);
                $t->dropColumn('eoffice_peminjaman_id');
            });
        }
    }
};
