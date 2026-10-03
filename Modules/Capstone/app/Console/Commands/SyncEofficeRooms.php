<?php

namespace Modules\Capstone\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\Location;
use Modules\EOffice\Models\Ruangan;

/**
 * EOffice adalah sumber tunggal ruangan. Command ini:
 *  1. Backfill eoffice_ruangan_id pada jadwal yang masih memakai location_id/nama lama.
 *  2. Menonaktifkan lokasi offline Capstone yang sudah terwakili di EOffice
 *     (tidak dihapus: histori + lokasi online tetap dipertahankan).
 */
class SyncEofficeRooms extends Command
{
    protected $signature = 'capstone:sync-eoffice-rooms {--dry-run : Tampilkan rencana tanpa mengubah data}';

    protected $description = 'Backfill ruangan EOffice ke jadwal Capstone + nonaktifkan lokasi offline duplikat.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $byName = Ruangan::pluck('id', 'nama');

        if ($byName->isEmpty()) {
            $this->warn('Tidak ada ruangan EOffice ditemukan.');

            return self::SUCCESS;
        }

        $linkOf = [];
        if (Schema::hasColumn('capstone_locations', 'eoffice_ruangan_id')) {
            $linkOf = Location::whereNotNull('eoffice_ruangan_id')->pluck('eoffice_ruangan_id', 'id')->all();
        }

        $backfilled = 0;
        foreach (['capstone_seminar_schedules', 'capstone_ta_defense_schedules'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'eoffice_ruangan_id')) {
                continue;
            }
            $rows = \Illuminate\Support\Facades\DB::table($table)->whereNull('eoffice_ruangan_id')->get(['id', 'room', 'location_id']);
            foreach ($rows as $row) {
                $id = $linkOf[$row->location_id] ?? null;
                $name = trim((string) ($row->room ?? ''));
                $id ??= ($name !== '' && isset($byName[$name]) ? $byName[$name] : null);
                if ($id) {
                    if (! $dry) {
                        \Illuminate\Support\Facades\DB::table($table)->where('id', $row->id)->update(['eoffice_ruangan_id' => $id]);
                    }
                    $backfilled++;
                } else {
                    $this->warn("{$table} #{$row->id}: '{$name}' tanpa padanan EOffice — dilewati.");
                }
            }
        }

        // Nonaktifkan lokasi offline yang namanya/link-nya sudah ada di EOffice.
        // Pencocokan di PHP (bukan whereIn SQL) agar nama numerik ('401') aman di Postgres.
        $linkedSet = array_flip(array_map('intval', array_values($linkOf)));
        $deactivateIds = Location::offline()->where('is_active', true)->get(['id', 'name', 'eoffice_ruangan_id'])
            ->filter(fn ($loc) => isset($byName[trim((string) $loc->name)])
                || ($loc->eoffice_ruangan_id && isset($linkedSet[(int) $loc->eoffice_ruangan_id])))
            ->pluck('id')->all();
        $deactivateCount = count($deactivateIds);
        if (! $dry && $deactivateCount > 0) {
            Location::whereIn('id', $deactivateIds)->update(['is_active' => false]);
        }

        $mode = $dry ? '[DRY-RUN] ' : '';
        $this->info("{$mode}Sync selesai: {$backfilled} jadwal di-backfill, {$deactivateCount} lokasi offline dinonaktifkan.");

        return self::SUCCESS;
    }
}
