<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\PracticumBookingRequest as Package;

/** Read MR decisions, without writing to MR or introducing reverse model relations. */
class PracticumBookingStatus
{
    public function sync(Package $package): Package
    {
        return DB::transaction(function () use ($package) {
            $package = Package::whereKey($package->id)->lockForUpdate()->firstOrFail();
            if ($package->kind !== 'new' || !$package->submitted_at || $package->status === 'draft') return $package;
            $meetings = $package->meetings()->with('booking')->get();
            $legacyDecision = $this->legacyDecision($package);
            $changed = false;
            foreach ($meetings as $meeting) {
                $booking = $meeting->booking;
                $matches = $booking && $booking->source === 'praktikum' && (string) $booking->getRawOriginal('source_id') === (string) $meeting->id;
                // Legacy package decisions predate source_id. Read only the exact inverse
                // booking link; never fabricate a reference or change an MR decision.
                $legacyMatch = $legacyDecision !== null && $booking && $booking->source === 'praktikum'
                    && $booking->getRawOriginal('source_id') === null
                    && (int) $booking->ruangan_id === (int) $meeting->room_id
                    && $booking->tanggal_pinjam->isSameDay($meeting->date)
                    && substr($booking->jam_mulai, 0, 5) === substr($meeting->starts_at, 0, 5)
                    && substr($booking->jam_selesai, 0, 5) === substr($meeting->ends_at, 0, 5);
                $state = !$matches && !$legacyMatch ? (!$meeting->booking_id && $legacyDecision === 'rejected' ? 'rejected' : 'pending') : match ($booking->status) {
                    'disetujui' => 'approved', 'ditolak' => 'rejected',
                    'dibatalkan' => 'cancelled', default => 'pending',
                };
                if ($meeting->state !== $state) { $meeting->update(['state' => $state]); $changed = true; }
            }
            if ($meetings->isEmpty()) return $package;
            $states = $meetings->pluck('state')->unique();
            $status = $states->count() === 1 ? $states->first() : ($states->contains('pending') ? 'pending' : 'partial');
            if ($package->status !== $status || $changed) {
                $restored = $legacyDecision !== null && $status === $legacyDecision;
                $history = $package->history ?? [];
                $history[] = ['at' => now()->toIso8601String(), 'actor' => 'Manajemen Ruang', 'actor_id' => null, 'action' => $restored ? 'legacy_status_restored' : 'booking_status_synced', 'status' => $status];
                $package->update(['status' => $status, 'history' => $history, 'version' => $package->version + 1]);
                if (!$restored) app(PracticumBookingWorkflow::class)->notify($package, true);
            }
            return $package->fresh();
        });
    }

    private function legacyDecision(Package $package): ?string
    {
        if (!$package->decided_at || !$package->decided_by) return null;
        // A later submission/withdrawal invalidates an earlier package decision.
        $event = collect($package->history ?? [])->last(fn ($event) => in_array($event['action'] ?? '',
            ['draft_saved', 'draft_created', 'submitted', 'withdrawn', 'approved', 'rejected'], true));
        if (!$event || (int) ($event['actor_id'] ?? 0) !== (int) $package->decided_by) return null;
        return in_array($event['action'], ['approved', 'rejected'], true) ? $event['action'] : null;
    }

    public function syncSubmitted(): void
    {
        Package::where('kind', 'new')->whereNotNull('submitted_at')->where('status', '!=', 'draft')
            ->chunkById(100, function ($packages) {
                foreach ($packages as $package) $this->sync($package);
            });
    }
}
