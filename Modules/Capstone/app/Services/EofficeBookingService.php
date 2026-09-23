<?php

namespace Modules\Capstone\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\EOffice\Models\Peminjaman;

/**
 * Write-side of the Capstone <-> EOffice room integration.
 *
 * Auto-creates `disetujui` EOffice bookings for Capstone schedules so the
 * EOffice calendar is blocked too (two-way). No EOffice admin approval step:
 * the Capstone scheduler is an admin (same trust level), and the booking is
 * clearly marked `[CAPSTONE ...]` with the creator recorded.
 */
class EofficeBookingService
{
    public function __construct(protected EofficeAvailabilityService $availability) {}

    public function syncForSeminar(SeminarSchedule $schedule): void
    {
        $schedule = $schedule->fresh() ?? $schedule;

        if (($this->col($schedule, 'status')) === 'CANCELLED') {
            $this->release($this->col($schedule, 'eoffice_peminjaman_id'));
            $this->clearReference($schedule);

            return;
        }

        $eofficeId = $this->directId($schedule) ?? $this->availability->resolveEofficeId(
            $this->col($schedule, 'room'),
            $this->col($schedule, 'location_id')
        );
        if (! $eofficeId) {
            $this->release($this->col($schedule, 'eoffice_peminjaman_id'));
            $this->clearReference($schedule);

            return;
        }

        $conflict = $this->availability->checkByEofficeId(
            $eofficeId,
            (string) $this->col($schedule, 'date'),
            (string) $this->col($schedule, 'start_time'),
            (string) $this->col($schedule, 'end_time'),
            $this->col($schedule, 'eoffice_peminjaman_id')
        );
        if ($conflict) {
            // Race lost: someone else booked EOffice first. Do not double-book;
            // leave the Capstone schedule without a linked booking for manual handling.
            Log::warning('Capstone EOffice auto-booking skipped (room taken)', [
                'schedule_type' => 'seminar',
                'schedule_id' => $schedule->id,
                'reason' => $conflict['message'],
            ]);
            $this->clearReference($schedule);

            return;
        }

        $label = "[CAPSTONE {$this->col($schedule, 'type')}] Kelompok #{$schedule->group_id} (jadwal #{$schedule->id})";
        $booking = $this->upsert(
            $this->col($schedule, 'eoffice_peminjaman_id'),
            $eofficeId,
            (string) $this->col($schedule, 'date'),
            (string) $this->col($schedule, 'start_time'),
            (string) $this->col($schedule, 'end_time'),
            $label,
            (int) $this->col($schedule, 'requested_by')
        );

        if ($booking && (int) $this->col($schedule, 'eoffice_peminjaman_id') !== (int) $booking->id) {
            $this->storeReference($schedule, $booking->id);
        }
    }

    public function syncForDefense(TaDefenseSchedule $schedule): void
    {
        $schedule = $schedule->fresh() ?? $schedule;

        if (($this->col($schedule, 'status')) === 'CANCELLED') {
            $this->release($this->col($schedule, 'eoffice_peminjaman_id'));
            $this->clearReference($schedule);

            return;
        }

        $eofficeId = $this->directId($schedule) ?? $this->availability->resolveEofficeId(
            $this->col($schedule, 'room'),
            $this->col($schedule, 'location_id')
        );
        if (! $eofficeId) {
            $this->release($this->col($schedule, 'eoffice_peminjaman_id'));
            $this->clearReference($schedule);

            return;
        }

        $conflict = $this->availability->checkByEofficeId(
            $eofficeId,
            (string) $this->col($schedule, 'date'),
            (string) $this->col($schedule, 'start_time'),
            (string) $this->col($schedule, 'end_time'),
            $this->col($schedule, 'eoffice_peminjaman_id')
        );
        if ($conflict) {
            Log::warning('Capstone EOffice auto-booking skipped (room taken)', [
                'schedule_type' => 'ta_defense',
                'schedule_id' => $schedule->id,
                'reason' => $conflict['message'],
            ]);
            $this->clearReference($schedule);

            return;
        }

        $label = "[CAPSTONE SIDANG-TA] Kelompok #{$schedule->group_id} (jadwal #{$schedule->id})";
        $booking = $this->upsert(
            $this->col($schedule, 'eoffice_peminjaman_id'),
            $eofficeId,
            (string) $this->col($schedule, 'date'),
            (string) $this->col($schedule, 'start_time'),
            (string) $this->col($schedule, 'end_time'),
            $label,
            (int) $this->col($schedule, 'requested_by')
        );

        if ($booking && (int) $this->col($schedule, 'eoffice_peminjaman_id') !== (int) $booking->id) {
            $this->storeReference($schedule, $booking->id);
        }
    }

    public function releaseForSeminar(SeminarSchedule $schedule): void
    {
        $this->release($this->storedBookingId($schedule));
    }

    public function releaseForDefense(TaDefenseSchedule $schedule): void
    {
        $this->release($this->storedBookingId($schedule));
    }

    public function syncForExpo(ExpoEvent $schedule): void
    {
        $schedule = $schedule->fresh() ?? $schedule;

        $eofficeId = $this->directId($schedule) ?? $this->availability->resolveEofficeId(
            $this->col($schedule, 'room'),
            null
        );
        if (! $eofficeId) {
            $this->release($this->col($schedule, 'eoffice_peminjaman_id'));
            $this->clearReference($schedule);

            return;
        }

        $conflict = $this->availability->checkByEofficeId(
            $eofficeId,
            (string) $this->col($schedule, 'date'),
            (string) $this->col($schedule, 'start_time'),
            (string) $this->col($schedule, 'end_time'),
            $this->col($schedule, 'eoffice_peminjaman_id')
        );
        if ($conflict) {
            Log::warning('Capstone EOffice auto-booking skipped (room taken)', [
                'schedule_type' => 'expo',
                'schedule_id' => $schedule->id,
                'reason' => $conflict['message'],
            ]);
            $this->clearReference($schedule);

            return;
        }

        $label = "[CAPSTONE EXPO] {$schedule->name} (event #{$schedule->id})";
        $booking = $this->upsert(
            $this->col($schedule, 'eoffice_peminjaman_id'),
            $eofficeId,
            (string) $this->col($schedule, 'date'),
            (string) $this->col($schedule, 'start_time'),
            (string) $this->col($schedule, 'end_time'),
            $label,
            (int) $this->col($schedule, 'created_by')
        );

        if ($booking && (int) $this->col($schedule, 'eoffice_peminjaman_id') !== (int) $booking->id) {
            $this->storeReference($schedule, $booking->id);
        }
    }

    public function releaseForExpo(ExpoEvent $schedule): void
    {
        $this->release($this->storedBookingId($schedule));
    }

    /**
     * Booking id as stored in the database, falling back to the in-memory
     * attribute. The model instance reaching `deleted` may be stale (the
     * reference is stored via a fresh copy), while a force-deleted row is
     * already gone from the table — hence the two-step lookup.
     */
    private function storedBookingId(SeminarSchedule|TaDefenseSchedule|ExpoEvent $schedule): ?int
    {
        if (Schema::hasColumn($schedule->getTable(), 'eoffice_peminjaman_id')) {
            $stored = DB::table($schedule->getTable())
                ->where('id', $schedule->getKey())
                ->value('eoffice_peminjaman_id');
            if ($stored) {
                return (int) $stored;
            }
        }

        $id = $this->col($schedule, 'eoffice_peminjaman_id');

        return $id ? (int) $id : null;
    }

    /**
     * Direct ruangan id stored on the schedule itself (single source:
     * EOffice). Falls back to legacy location/name resolution.
     */
    private function directId(SeminarSchedule|TaDefenseSchedule|ExpoEvent $schedule): ?int
    {
        if (! \Illuminate\Support\Facades\Schema::hasColumn($schedule->getTable(), 'eoffice_ruangan_id')) {
            return null;
        }

        $id = $this->col($schedule, 'eoffice_ruangan_id');

        return $id ? (int) $id : null;
    }

    private function upsert(
        ?int $existingId,
        int $eofficeId,
        string $date,
        string $startTime,
        string $endTime,
        string $tujuan,
        int $requestedBy
    ): ?Peminjaman {
        if (! $this->availability->isAvailable()) {
            return null;
        }

        $actorId = $requestedBy > 0 ? $requestedBy : $this->fallbackActorId();
        if (! $actorId) {
            Log::warning('Capstone EOffice auto-booking skipped (no actor user found)');

            return null;
        }

        $payload = [
            'user_id' => $actorId,
            'ruangan_id' => $eofficeId,
            'tujuan' => $tujuan,
            'tanggal_pinjam' => $this->availability->normalizeDate($date),
            'jam_mulai' => $this->availability->normalizeTime($startTime),
            'jam_selesai' => $this->availability->normalizeTime($endTime),
            'status' => 'disetujui',
            'waktu_approval' => now(),
            'created_by' => $actorId,
        ];

        if ($existingId) {
            $booking = Peminjaman::find($existingId);
            if ($booking) {
                $booking->update($payload);

                return $booking->fresh();
            }
        }

        return Peminjaman::create($payload);
    }

    private function release(?int $peminjamanId): void
    {
        if (! $peminjamanId || ! $this->availability->isAvailable()) {
            return;
        }

        Peminjaman::whereKey($peminjamanId)
            ->where('tujuan', 'like', '[CAPSTONE%')
            ->delete();
    }

    private function clearReference(SeminarSchedule|TaDefenseSchedule|ExpoEvent $schedule): void
    {
        if (! $this->col($schedule, 'eoffice_peminjaman_id')) {
            return;
        }

        $this->storeReference($schedule, null);
    }

    private function storeReference(SeminarSchedule|TaDefenseSchedule|ExpoEvent $schedule, ?int $peminjamanId): void
    {
        // Guard for schemas predating the link migration.
        if (! \Illuminate\Support\Facades\Schema::hasColumn($schedule->getTable(), 'eoffice_peminjaman_id')) {
            return;
        }

        $schedule::withoutEvents(fn () => $schedule->forceFill([
            'eoffice_peminjaman_id' => $peminjamanId,
        ])->saveQuietly());
    }

    /**
     * Defensive attribute read: the link columns may not exist on older
     * schemas (isolated test tables), where direct access would throw.
     */
    private function col(SeminarSchedule|TaDefenseSchedule|ExpoEvent $schedule, string $key): mixed
    {
        $attributes = $schedule->getAttributes();

        return $attributes[$key] ?? null;
    }

    private function fallbackActorId(): ?int
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['superadmin', 'admin_capstone', 'admin']))
            ->orderBy('id')
            ->value('id');
    }
}
