<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Models\Vote;
use Modules\ManajemenMahasiswa\Models\XpLog;

class ThreadService
{
    public function __construct(
        private GamificationService $gamificationService,
    ) {}

    // =========================================================================
    // List
    // =========================================================================

    /**
     * Paginate threads — pinned di atas, lalu terbaru.
     */
    public function listThreads(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Thread::with(['author'])
            ->withCount('comments')
            ->when(isset($filters['search']) && $filters['search'], fn($q) => $q->search($filters['search']))
            ->when(isset($filters['kategori']) && $filters['kategori'] && $filters['kategori'] !== 'semua',
                fn($q) => $q->byKategori($filters['kategori']))
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    // =========================================================================
    // CRUD
    // =========================================================================

    public function findThread(int $id): Thread
    {
        return Thread::with(['author', 'bestAnswer.author'])
            ->withCount('comments')
            ->findOrFail($id);
    }

    public function createThread(int $userId, array $data): Thread
    {
        return DB::transaction(function () use ($userId, $data) {
            $thread = Thread::create([
                'user_id'  => $userId,
                'judul'    => $data['judul'],
                'konten'   => $data['konten'],
                'kategori' => $data['kategori'],
                'is_pinned' => false,
                'is_locked' => false,
                'vote_count' => 0,
                'comment_count' => 0,
            ]);

            // Award XP untuk buat thread
            $this->gamificationService->awardXp($userId, XpLog::ACTION_CREATE_THREAD, $thread);

            // Record streak
            $this->gamificationService->recordStreak($userId);

            return $thread;
        });
    }

    public function deleteThread(int $id, int $userId, bool $isAdmin = false): void
    {
        $thread = Thread::findOrFail($id);

        if (!$isAdmin && $thread->user_id !== $userId) {
            throw new \RuntimeException('Tidak memiliki akses untuk menghapus thread ini.');
        }

        DB::transaction(fn() => $thread->delete());
    }

    // =========================================================================
    // Voting
    // =========================================================================

    /**
     * Toggle vote untuk thread.
     * Return: ['vote_count' => int, 'user_vote' => int|null]
     */
    public function vote(int $userId, int $threadId, int $value): array
    {
        $thread = Thread::findOrFail($threadId);
        $value  = $value > 0 ? 1 : -1;

        $existingVote = Vote::where('user_id', $userId)
            ->where('voteable_type', Thread::class)
            ->where('voteable_id', $threadId)
            ->first();

        DB::transaction(function () use ($existingVote, $userId, $thread, $threadId, $value) {
            if ($existingVote) {
                if ($existingVote->value === $value) {
                    // Sama vote → hapus (toggle off)
                    $existingVote->delete();
                } else {
                    // Beda vote → update
                    $existingVote->update(['value' => $value]);
                }
            } else {
                // Vote baru
                Vote::create([
                    'user_id'       => $userId,
                    'voteable_type' => Thread::class,
                    'voteable_id'   => $threadId,
                    'value'         => $value,
                ]);

                // Award XP ke pemilik thread jika upvote
                if ($value === 1 && $thread->user_id !== $userId) {
                    $this->gamificationService->awardXp(
                        $thread->user_id,
                        XpLog::ACTION_RECEIVE_UPVOTE,
                        $thread
                    );
                }
            }

            // Sync cached vote count
            $thread->syncVoteCount();
        });

        $thread->refresh();
        $currentVote = Vote::where('user_id', $userId)
            ->where('voteable_type', Thread::class)
            ->where('voteable_id', $threadId)
            ->first();

        return [
            'vote_count' => $thread->vote_count,
            'user_vote'  => $currentVote?->value,
        ];
    }

    // =========================================================================
    // Moderation
    // =========================================================================

    public function pinThread(int $id): Thread
    {
        $thread = Thread::findOrFail($id);
        $thread->update(['is_pinned' => !$thread->is_pinned]);
        return $thread->fresh();
    }

    public function lockThread(int $id): Thread
    {
        $thread = Thread::findOrFail($id);
        $thread->update(['is_locked' => !$thread->is_locked]);
        return $thread->fresh();
    }

    public function markBestAnswer(int $threadId, int $commentId, int $userId): void
    {
        $thread = Thread::findOrFail($threadId);

        // Hanya pemilik thread atau admin yang bisa mark best answer
        if ($thread->user_id !== $userId) {
            throw new \RuntimeException('Hanya pemilik thread yang bisa memilih jawaban terbaik.');
        }

        DB::transaction(function () use ($thread, $commentId) {
            // Reset previous best answer
            if ($thread->best_answer_id) {
                $thread->bestAnswer?->update(['is_best_answer' => false]);
            }

            $comment = \Modules\ManajemenMahasiswa\Models\Comment::findOrFail($commentId);
            $comment->update(['is_best_answer' => true]);
            $thread->update(['best_answer_id' => $commentId]);

            // Award XP ke commenter
            $this->gamificationService->awardXp(
                $comment->user_id,
                XpLog::ACTION_BEST_ANSWER,
                $comment
            );
        });
    }
}
