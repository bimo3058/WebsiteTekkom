<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\Location;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;

/**
 * Read-side of the Capstone <-> EOffice room integration.
 *
 * Single source of truth for "is this room busy in EOffice?".
 * Blocking rule (agreed): only `disetujui` peminjaman + internal
 * schedules block. `menunggu`/`ditolak` do not block.
 *
 * Time overlap logic mirrors EOffice UserPeminjamanController:
 *   jam_mulai < requested_end AND jam_selesai > requested_start
 */
class EofficeAvailabilityService
{
    /**
     * Resolve a Capstone room reference to an EOffice ruangan id.
     *
     * Priority: explicit eoffice_ruangan_id > location_id link > exact
     * name match on linked locations > exact name match on EOffice rooms.
     * Returns null for online locations or unresolvable rooms.
     */
    public function resolveEofficeId(?string $room = null, ?int $locationId = null, ?int $eofficeId = null): ?int
    {
        if (! $this->isAvailable()) {
            return null;
        }

        if ($eofficeId && Ruangan::whereKey($eofficeId)->exists()) {
            return $eofficeId;
        }

        if ($locationId) {
            $location = Location::find($locationId);
            if (! $location || $location->isOnline()) {
                return null;
            }
            // Defensive: link column may not exist on older schemas.
            $locationAttributes = $location->getAttributes();
            $linkedId = $locationAttributes['eoffice_ruangan_id'] ?? null;
            if ($linkedId) {
                return (int) $linkedId;
            }
            $room ??= $location->name;
        }

        if (empty($room)) {
            return null;
        }

        $hasLinkColumn = Schema::hasColumn('capstone_locations', 'eoffice_ruangan_id');

        $linked = $hasLinkColumn
            ? Location::where('name', $room)->whereNotNull('eoffice_ruangan_id')->first()
            : null;
        if ($linked) {
            $location = $locationId ? Location::find($locationId) : null;
            if ($location && $location->isOnline()) {
                return null;
            }

            $linkedAttributes = $linked->getAttributes();

            return (int) ($linkedAttributes['eoffice_ruangan_id'] ?? 0);
        }

        $ruangan = Ruangan::where('nama', $room)->first();

        return $ruangan ? (int) $ruangan->id : null;
    }

    /**
     * Check a single EOffice room for the given slot.
     *
     * @return array|null conflict info ['source' => 'peminjaman'|'internal', 'message' => ...]
     */
    public function checkByEofficeId(
        int $eofficeId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludePeminjamanId = null
    ): ?array {
        if (! $this->isAvailable()) {
            return null;
        }

        $date = $this->normalizeDate($date);
        $startTime = $this->normalizeTime($startTime);
        $endTime = $this->normalizeTime($endTime);

        $conflict = Peminjaman::where('ruangan_id', $eofficeId)
            ->whereDate('tanggal_pinjam', $date)
            ->where('status', 'disetujui')
            ->when($excludePeminjamanId, fn ($q) => $q->where('id', '!=', $excludePeminjamanId))
            ->where('jam_mulai', '<', $endTime)
            ->where('jam_selesai', '>', $startTime)
            ->first();

        if ($conflict) {
            $range = substr((string) $conflict->jam_mulai, 0, 5).'–'.substr((string) $conflict->jam_selesai, 0, 5);

            return [
                'source' => 'peminjaman',
                'ruangan_id' => $eofficeId,
                'message' => "Ruangan '{$conflict->ruangan?->nama}' sudah dipesan pihak lain di EOffice pada {$date} ({$range}).",
            ];
        }

        $internal = $this->internalConflict($eofficeId, $date, $startTime, $endTime);
        if ($internal) {
            $range = substr((string) $internal->jam_mulai, 0, 5).'–'.substr((string) $internal->jam_selesai, 0, 5);

            return [
                'source' => 'internal',
                'ruangan_id' => $eofficeId,
                'message' => "Ruangan terblokir jadwal {$internal->kategori} di EOffice: {$internal->keterangan} ({$range}).",
            ];
        }

        return null;
    }

    /**
     * Check by Capstone room name / location id.
     */
    public function checkByRoom(
        ?string $room = null,
        ?int $locationId = null,
        ?string $date = null,
        ?string $startTime = null,
        ?string $endTime = null,
        ?int $excludePeminjamanId = null,
        ?int $eofficeId = null
    ): ?array {
        if ($date === null || $startTime === null || $endTime === null) {
            return null;
        }

        $eofficeId = $this->resolveEofficeId($room, $locationId, $eofficeId);
        if (! $eofficeId) {
            return null;
        }

        return $this->checkByEofficeId($eofficeId, $date, $startTime, $endTime, $excludePeminjamanId);
    }

    /**
     * All EOffice ruangan ids busy in the given slot.
     *
     * @return int[]
     */
    public function busyEofficeIds(string $date, string $startTime, string $endTime): array
    {
        if (! $this->isAvailable()) {
            return [];
        }

        $date = $this->normalizeDate($date);
        $startTime = $this->normalizeTime($startTime);
        $endTime = $this->normalizeTime($endTime);
        $dayOfWeek = \Carbon\Carbon::parse($date)->format('N');

        $busyFromBookings = Peminjaman::whereDate('tanggal_pinjam', $date)
            ->where('status', 'disetujui')
            ->where('jam_mulai', '<', $endTime)
            ->where('jam_selesai', '>', $startTime)
            ->pluck('ruangan_id')
            ->all();

        $busyFromInternal = MrJadwalInternal::where(function ($query) use ($date, $dayOfWeek) {
            $query->where(function ($q) use ($dayOfWeek, $date) {
                $q->where('tipe_jadwal', 'rutin')
                    ->where('hari', $dayOfWeek)
                    ->where(function ($tq) use ($date) {
                        $tq->whereNull('tgl_mulai_efektif')
                            ->orWhere('tgl_mulai_efektif', '<=', $date);
                    })
                    ->where(function ($tq) use ($date) {
                        $tq->whereNull('tgl_selesai_efektif')
                            ->orWhere('tgl_selesai_efektif', '>=', $date);
                    });
            })->orWhere(function ($q) use ($date) {
                $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $date);
            });
        })
            ->where('jam_mulai', '<', $endTime)
            ->where('jam_selesai', '>', $startTime)
            ->pluck('ruangan_id')
            ->all();

        return array_values(array_unique(array_merge($busyFromBookings, $busyFromInternal)));
    }

    /**
     * Map busy EOffice ids to Capstone location names (linked + name match).
     *
     * @return string[]
     */
    public function busyLocationNames(string $date, string $startTime, string $endTime): array
    {
        $busyIds = $this->busyEofficeIds($date, $startTime, $endTime);
        if (empty($busyIds)) {
            return [];
        }

        $names = Schema::hasColumn('capstone_locations', 'eoffice_ruangan_id')
            ? Location::whereIn('eoffice_ruangan_id', $busyIds)->pluck('name')->all()
            : [];

        $roomNames = Ruangan::whereIn('id', $busyIds)->pluck('nama')->all();
        if (! empty($roomNames)) {
            $unlinked = Location::whereIn('name', $roomNames)
                ->whereNull('eoffice_ruangan_id')
                ->pluck('name')
                ->all();
            $names = array_merge($names, $unlinked);
        }

        return array_values(array_unique($names));
    }

    public function isAvailable(): bool
    {
        return Schema::hasTable('eo_mr_ruangans')
            && Schema::hasTable('eo_mr_peminjamans')
            && Schema::hasTable('eo_mr_jadwal_internal');
    }

    public function normalizeDate(string $date): string
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }

    public function normalizeTime(string $time): string
    {
        $time = trim($time);

        return strlen($time) === 5 ? $time.':00' : substr($time, 0, 8);
    }

    private function internalConflict(int $eofficeId, string $date, string $startTime, string $endTime): ?MrJadwalInternal
    {
        $dayOfWeek = \Carbon\Carbon::parse($date)->format('N');

        return MrJadwalInternal::where('ruangan_id', $eofficeId)
            ->where(function ($query) use ($date, $dayOfWeek) {
                $query->where(function ($q) use ($dayOfWeek, $date) {
                    $q->where('tipe_jadwal', 'rutin')
                        ->where('hari', $dayOfWeek)
                        ->where(function ($tq) use ($date) {
                            $tq->whereNull('tgl_mulai_efektif')
                                ->orWhere('tgl_mulai_efektif', '<=', $date);
                        })
                        ->where(function ($tq) use ($date) {
                            $tq->whereNull('tgl_selesai_efektif')
                                ->orWhere('tgl_selesai_efektif', '>=', $date);
                        });
                })->orWhere(function ($q) use ($date) {
                    $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $date);
                });
            })
            ->where('jam_mulai', '<', $endTime)
            ->where('jam_selesai', '>', $startTime)
            ->first();
    }
}
