<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone <-> EOffice room integration.
 *
 * - capstone_locations.eoffice_ruangan_id: link (by ID) ke eo_mr_ruangans.
 *   Lokasi online (virtual) dibiarkan NULL dan tidak dicek ke EOffice.
 * - capstone_{seminar,ta_defense}_schedules.eoffice_peminjaman_id: pelacak
 *   baris auto-booking di eo_mr_peminjamans (dua arah).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capstone_locations', function (Blueprint $table) {
            if (! Schema::hasColumn('capstone_locations', 'eoffice_ruangan_id')) {
                $table->unsignedBigInteger('eoffice_ruangan_id')->nullable()->after('id');
                $table->index('eoffice_ruangan_id');
            }
        });

        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('capstone_seminar_schedules', 'eoffice_peminjaman_id')) {
                $table->unsignedBigInteger('eoffice_peminjaman_id')->nullable()->after('location_id');
                $table->index('eoffice_peminjaman_id');
            }
        });

        Schema::table('capstone_ta_defense_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('capstone_ta_defense_schedules', 'eoffice_peminjaman_id')) {
                $table->unsignedBigInteger('eoffice_peminjaman_id')->nullable()->after('location_id');
                $table->index('eoffice_peminjaman_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('capstone_ta_defense_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('capstone_ta_defense_schedules', 'eoffice_peminjaman_id')) {
                $table->dropIndex(['eoffice_peminjaman_id']);
                $table->dropColumn('eoffice_peminjaman_id');
            }
        });

        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('capstone_seminar_schedules', 'eoffice_peminjaman_id')) {
                $table->dropIndex(['eoffice_peminjaman_id']);
                $table->dropColumn('eoffice_peminjaman_id');
            }
        });

        Schema::table('capstone_locations', function (Blueprint $table) {
            if (Schema::hasColumn('capstone_locations', 'eoffice_ruangan_id')) {
                $table->dropIndex(['eoffice_ruangan_id']);
                $table->dropColumn('eoffice_ruangan_id');
            }
        });
    }
};
