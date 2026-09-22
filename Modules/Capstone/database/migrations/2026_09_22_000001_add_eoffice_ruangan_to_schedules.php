<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Jadwal Capstone memakai ruangan EOffice secara langsung.
     * Backfill: location_id (link) > exact-match room = ruangan.nama.
     * Baris yang tidak cocok dibiarkan NULL + dicatat ke log.
     */
    public function up(): void
    {
        foreach (['capstone_seminar_schedules', 'capstone_ta_defense_schedules', 'capstone_expo_events'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'eoffice_ruangan_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('eoffice_ruangan_id')->nullable()->after('room');
                    $t->foreign('eoffice_ruangan_id')->references('id')->on('eo_mr_ruangans')->nullOnDelete();
                });
            }
        }

        $this->backfill('capstone_seminar_schedules');
        $this->backfill('capstone_ta_defense_schedules');
        $this->backfill('capstone_expo_events', false);
    }

    public function down(): void
    {
        foreach (['capstone_seminar_schedules', 'capstone_ta_defense_schedules', 'capstone_expo_events'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'eoffice_ruangan_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['eoffice_ruangan_id']);
                    $t->dropColumn('eoffice_ruangan_id');
                });
            }
        }
    }

    private function backfill(string $table, bool $hasLocationId = true): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'eoffice_ruangan_id')) {
            return;
        }
        if (! Schema::hasTable('eo_mr_ruangans') || ! Schema::hasTable('capstone_locations')) {
            return;
        }

        // 1) Via link lokasi: location_id -> capstone_locations.eoffice_ruangan_id
        if ($hasLocationId && Schema::hasColumn($table, 'location_id')) {
            $map = DB::table('capstone_locations')->whereNotNull('eoffice_ruangan_id')->pluck('eoffice_ruangan_id', 'id');
            foreach ($map as $locationId => $ruanganId) {
                DB::table($table)
                    ->whereNull('eoffice_ruangan_id')
                    ->where('location_id', $locationId)
                    ->update(['eoffice_ruangan_id' => $ruanganId]);
            }
        }

        // 2) Exact-match nama: room = eo_mr_ruangans.nama
        $unmatched = DB::table($table)->whereNull('eoffice_ruangan_id')->get(['id', 'room']);
        $byName = DB::table('eo_mr_ruangans')->pluck('id', 'nama');
        $missed = 0;
        foreach ($unmatched as $row) {
            $name = trim((string) ($row->room ?? ''));
            if ($name !== '' && isset($byName[$name])) {
                DB::table($table)->where('id', $row->id)->update(['eoffice_ruangan_id' => $byName[$name]]);
            } else {
                $missed++;
            }
        }

        if ($missed > 0) {
            Log::warning("[capstone-eoffice-backfill] {$table}: {$missed} baris tanpa padanan ruangan EOffice (eoffice_ruangan_id tetap NULL).");
        }
    }
};
