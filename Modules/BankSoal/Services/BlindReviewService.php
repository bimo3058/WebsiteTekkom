<?php

namespace Modules\BankSoal\Services;

use App\Models\User;
use App\Notifications\BlindReviewAssignedNotification;
use App\Notifications\BlindReviewCompletedAdminNotification;
use App\Notifications\BlindReviewItemReviewedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Modules\BankSoal\Models\BlindReviewItem;
use Modules\BankSoal\Models\BlindReviewRound;
use Modules\BankSoal\Models\DosenPengampuMk;
use Modules\BankSoal\Models\MataKuliah;

class BlindReviewService
{
    /**
     * Buat round blind review baru untuk sekumpulan soal.
     * Deadline otomatis 2 hari dari sekarang.
     */
    public function createRoundForQuestions(int $mkId, array $questionIds, int $creatorId, int $requiredReviewers = 1, bool $isBlind = true, array $ujianMeta = []): ?BlindReviewRound
    {
        if (!Schema::hasTable('bs_blind_review_rounds') || !Schema::hasTable('bs_blind_review_items')) {
            return null;
        }

        $questionIds = collect($questionIds)->filter()->map(fn ($id) => (int) $id)->unique()->values();

        if ($questionIds->isEmpty()) {
            return null;
        }

        $reviewerIds = DosenPengampuMk::query()
            ->where('mk_id', $mkId)
            ->where('user_id', '!=', $creatorId)
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($reviewerIds->isEmpty()) {
            return null;
        }

        $required = max(1, min((int) $requiredReviewers, $reviewerIds->count()));

        $round = BlindReviewRound::create([
            'mk_id'              => $mkId,
            'created_by'         => $creatorId,
            'nama_round'         => 'Blind Review ' . now()->format('Y-m-d H:i'),
            'required_reviewers' => $required,
            'is_blind'           => $isBlind,
            'status'             => 'in_review',
            'deadline_at'        => now()->addDays(2),
            'ujian_meta'         => !empty($ujianMeta) ? $ujianMeta : null,
        ]);

        $reviewerQueue = $reviewerIds->values();
        $cursor = 0;
        $notifications = collect();

        foreach ($questionIds as $questionId) {
            for ($i = 0; $i < $required; $i++) {
                $reviewerId = $reviewerQueue[$cursor % $reviewerQueue->count()];
                $cursor++;

                $item = BlindReviewItem::create([
                    'round_id'       => $round->id,
                    'pertanyaan_id'  => $questionId,
                    'reviewer_id'    => $reviewerId,
                    'assigned_by'    => $creatorId,
                    'is_blind'       => $isBlind,
                    'status'         => 'pending',
                ]);

                $notifications->push([$item->id, $reviewerId]);
            }
        }

        $mataKuliah = MataKuliah::find($mkId);
        $creator    = User::find($creatorId);

        foreach ($notifications as [$itemId, $reviewerId]) {
            $reviewer = User::find($reviewerId);
            if ($reviewer) {
                $reviewer->notify(new BlindReviewAssignedNotification(
                    $itemId,
                    $mataKuliah?->nama ?? 'Mata Kuliah',
                    $creator?->name ?? 'Dosen Pengampu'
                ));
            }
        }

        return $round;
    }

    /**
     * Update status round setelah satu item di-review.
     * Kirim notifikasi ke creator dan ke admin jika round completed.
     */
    public function updateRoundStatus(BlindReviewRound $round, ?BlindReviewItem $reviewedItem = null): void
    {
        // Kirim notif ke creator setiap kali ada item selesai direview
        if ($reviewedItem && $round->created_by) {
            $creator  = User::find($round->created_by);
            $reviewer = User::find($reviewedItem->reviewer_id);
            $mk       = $round->mataKuliah ?? MataKuliah::find($round->mk_id);

            if ($creator && $reviewer) {
                $creator->notify(new BlindReviewItemReviewedNotification(
                    $round->id,
                    $mk?->nama ?? 'Mata Kuliah',
                    $reviewer->name,
                    $reviewedItem->status,
                    $reviewedItem->catatan
                ));
            }
        }

        // Refresh round dari DB agar tidak pakai relasi lama yang stale di memori
        $round->refresh();
        $total = $round->items()->count();
        $done  = $round->items()->whereIn('status', ['approved', 'rejected'])->count();

        // Round selesai jika semua item sudah direview
        if ($total > 0 && $done === $total) {
            $round->update(['status' => 'completed']);
            $this->notifyAdminRoundCompleted($round, false);
            return;
        }

        $round->update(['status' => 'in_review']);
    }

    /**
     * Auto-approve semua item yang masih pending jika deadline sudah lewat.
     * Dipanggil oleh scheduler harian.
     */
    public function autoApproveExpiredRounds(): int
    {
        if (!Schema::hasTable('bs_blind_review_rounds')) {
            return 0;
        }

        $expiredRounds = BlindReviewRound::query()
            ->where('status', 'in_review')
            ->where('deadline_at', '<=', now())
            ->get();

        $processed = 0;

        foreach ($expiredRounds as $round) {
            // Auto-approve semua item yang masih pending
            $round->items()
                ->where('status', 'pending')
                ->update([
                    'status'      => 'approved',
                    'reviewed_at' => now(),
                    'catatan'     => 'Auto-approved: batas waktu review terlewati.',
                ]);

            $round->update(['status' => 'completed']);
            $this->notifyAdminRoundCompleted($round, true);
            $processed++;
        }

        return $processed;
    }

    /**
     * Kirim notifikasi ke semua admin dan buat antrian cetak di PenarikanSoal.
     */
    private function notifyAdminRoundCompleted(BlindReviewRound $round, bool $autoApproved): void
    {
        $round->loadMissing(['mataKuliah', 'creator']);
        $mk      = $round->mataKuliah;
        $creator = $round->creator;

        // Buat record PenarikanSoal agar muncul di antrian cetak admin
        $soalIds = $round->items()->pluck('pertanyaan_id')->unique()->values();
        $meta    = $round->ujian_meta ?? [];

        $soalData = \Modules\BankSoal\Models\Pertanyaan::with(['cpl', 'cpmk'])
            ->whereIn('id', $soalIds)
            ->get()
            ->map(fn ($s, $i) => [
                'nomor' => $i + 1,
                'id'    => $s->id,
                'soal'  => $s->soal,
                'cpl'   => $s->cpl?->kode,
                'cpmk'  => $s->cpmk?->kode,
                'bobot' => $s->bobot ?? 0,
            ])->values()->toArray();

        \Modules\BankSoal\Models\PenarikanSoal::create([
            'dosen_id'       => $round->created_by,
            'mk_id'          => $round->mk_id,
            'nama_ekstraksi' => ($mk?->nama ?? 'MK') . ' - ' . ($meta['agenda'] ?? 'Ujian'),
            'tipe_ujian'     => str_contains(strtolower($meta['agenda'] ?? ''), 'uts') ? 'uts' : (str_contains(strtolower($meta['agenda'] ?? ''), 'kuis') ? 'kuis' : 'uas'),
            'tahun_akademik' => $meta['tahun_ajaran'] ?? date('Y') . '/' . (date('Y') + 1),
            'semester'       => $meta['semester'] ?? 'Ganjil',
            'tanggal_ujian'  => $meta['hari_tanggal'] ?? null,
            'soal_data'      => $soalData,
            'jumlah_soal'    => count($soalData),
            'total_bobot'    => collect($soalData)->sum('bobot'),
            'status'         => 'pending',
            'metode_ujian'   => 'offline',
            'status_cetak'   => 'pending',
            'catatan_internal' => $autoApproved ? 'Auto-approved: deadline blind review tercapai.' : null,
        ]);

        // Notifikasi ke admin
        $admins = User::whereHas('roles', fn ($q) =>
            $q->whereIn('name', ['admin', 'superadmin'])
        )->get();

        foreach ($admins as $admin) {
            $admin->notify(new BlindReviewCompletedAdminNotification(
                $round->id,
                $mk?->nama ?? 'Mata Kuliah',
                $creator?->name ?? 'Dosen',
                $autoApproved
            ));
        }
    }

    public function getAssignedItemsForReviewer(int $reviewerId): Collection
    {
        if (!Schema::hasTable('bs_blind_review_items')) {
            return collect();
        }

        return BlindReviewItem::query()
            ->with(['round.mataKuliah', 'round.creator', 'pertanyaan.mataKuliah'])
            ->where('reviewer_id', $reviewerId)
            ->latest('created_at')
            ->get();
    }
}
