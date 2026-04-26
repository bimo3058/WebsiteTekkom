<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Comment;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Models\Vote;
use Modules\ManajemenMahasiswa\Models\XpLog;

class CommentService
{
    public function __construct(
        private GamificationService $gamificationService,
    ) {}

    // =========================================================================
    // List
    // =========================================================================

    /**
     * Paginate top-level comments untuk satu thread (replies di-eager-load).
     */
    public function listComments(int $threadId, int $perPage = 20): LengthAwarePaginator
    {
        return Comment::with(['author', 'allReplies.author'])
            ->where('thread_id', $threadId)
            ->whereNull('parent_id')
            ->orderByDesc('is_best_answer')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    // =========================================================================
    // CRUD
    // =========================================================================

    public function addComment(int $userId, int $threadId, string $konten, ?int $parentId = null): Comment
    {
        $thread = Thread::findOrFail($threadId);

        if ($thread->is_locked) {
            throw new \RuntimeException('Thread sudah dikunci, tidak bisa menambahkan komentar.');
        }

        return DB::transaction(function () use ($userId, $threadId, $konten, $parentId, $thread) {
            $comment = Comment::create([
                'thread_id' => $threadId,
                'user_id'   => $userId,
                'parent_id' => $parentId,
                'konten'    => $konten,
                'vote_count' => 0,
            ]);

            // Update cached comment count
            $thread->syncCommentCount();

            // Award XP
            $this->gamificationService->awardXp($userId, XpLog::ACTION_COMMENT, $comment);

            // Record streak
            $this->gamificationService->recordStreak($userId);

            return $comment->load('author');
        });
    }

    public function updateComment(int $commentId, int $userId, string $konten): Comment
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== $userId) {
            throw new \RuntimeException('Tidak memiliki akses untuk mengedit komentar ini.');
        }

        $comment->update([
            'konten' => $konten,
        ]);

        return $comment;
    }

    public function deleteComment(int $commentId, int $userId, bool $isAdmin = false): void
    {
        $comment = Comment::findOrFail($commentId);

        if (!$isAdmin && $comment->user_id !== $userId) {
            throw new \RuntimeException('Tidak memiliki akses untuk menghapus komentar ini.');
        }

        DB::transaction(function () use ($comment) {
            $threadId = $comment->thread_id;
            $comment->delete();

            // Update cached comment count
            Thread::find($threadId)?->syncCommentCount();
        });
    }

    // =========================================================================
    // Voting
    // =========================================================================

    /**
     * Toggle vote untuk comment.
     */
    public function voteComment(int $userId, int $commentId, int $value): array
    {
        $comment = Comment::findOrFail($commentId);
        $value   = $value > 0 ? 1 : -1;

        /** @var \Modules\ManajemenMahasiswa\Models\Vote|null $existingVote */
        $existingVote = Vote::where('user_id', $userId)
            ->where('voteable_type', Comment::class)
            ->where('voteable_id', $commentId)
            ->first();

        DB::transaction(function () use ($existingVote, $userId, $comment, $commentId, $value) {
            if ($existingVote) {
                if ($existingVote->value === $value) {
                    $existingVote->delete();
                } else {
                    $existingVote->update(['value' => $value]);
                }
            } else {
                Vote::create([
                    'user_id'       => $userId,
                    'voteable_type' => Comment::class,
                    'voteable_id'   => $commentId,
                    'value'         => $value,
                ]);

                // Award XP ke pemilik comment jika upvote
                if ($value === 1 && $comment->user_id !== $userId) {
                    $this->gamificationService->awardXp(
                        $comment->user_id,
                        XpLog::ACTION_RECEIVE_UPVOTE,
                        $comment
                    );
                }

                // Penalti -1 XP untuk pemberi downvote
                if ($value === -1) {
                    $this->gamificationService->penalizeDownvote($userId, $comment);
                }
            }

            $comment->syncVoteCount();
        });

        $comment->refresh();
        $currentVote = Vote::where('user_id', $userId)
            ->where('voteable_type', Comment::class)
            ->where('voteable_id', $commentId)
            ->first();

        return [
            'vote_count' => $comment->vote_count,
            'user_vote'  => $currentVote?->value,
        ];
    }
}
