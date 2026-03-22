<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\ForumMahasiswa;
use Modules\ManajemenMahasiswa\Models\Discussion;
use Modules\ManajemenMahasiswa\Models\CommentForum;

class ForumService
{
    // =========================================================================
    // Forum
    // =========================================================================

    public function listForums(): \Illuminate\Database\Eloquent\Collection
    {
        return ForumMahasiswa::withCount('discussions')->get();
    }

    public function findForum(int $id): ForumMahasiswa
    {
        return ForumMahasiswa::findOrFail($id);
    }

    public function createForum(array $data): ForumMahasiswa
    {
        return ForumMahasiswa::create($data);
    }

    public function updateForum(int $id, array $data): ForumMahasiswa
    {
        $forum = ForumMahasiswa::findOrFail($id);
        $forum->update($data);
        return $forum->fresh();
    }

    public function deleteForum(int $id): void
    {
        ForumMahasiswa::findOrFail($id)->delete();
    }

    // =========================================================================
    // Discussion
    // =========================================================================

    /**
     * Listing diskusi dalam satu forum — pinned ditaruh paling atas.
     */
    public function listDiscussions(int $forumId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Discussion::with(['author', 'forum'])
            ->withCount('comments')
            ->byForum($forumId)
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->orderByRaw("FIELD(status, ?, ?) ASC", [Discussion::STATUS_PINNED, Discussion::STATUS_OPEN])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findDiscussion(int $id): Discussion
    {
        return Discussion::with(['author', 'forum'])->withCount('comments')->findOrFail($id);
    }

    public function createDiscussion(int $userId, int $forumId, array $data): Discussion
    {
        return DB::transaction(fn() => Discussion::create(array_merge($data, [
            'user_id'  => $userId,
            'forum_id' => $forumId,
            'status'   => Discussion::STATUS_OPEN,
        ])));
    }

    public function updateDiscussion(int $id, array $data): Discussion
    {
        return DB::transaction(function () use ($id, $data) {
            $discussion = Discussion::findOrFail($id);
            $discussion->update($data);
            return $discussion->fresh();
        });
    }

    public function deleteDiscussion(int $id): void
    {
        DB::transaction(fn() => Discussion::findOrFail($id)->delete());
    }

    public function pinDiscussion(int $id): Discussion
    {
        return $this->updateDiscussion($id, ['status' => Discussion::STATUS_PINNED]);
    }

    public function closeDiscussion(int $id): Discussion
    {
        return $this->updateDiscussion($id, ['status' => Discussion::STATUS_CLOSED]);
    }

    // =========================================================================
    // Comment
    // =========================================================================

    /**
     * Listing komentar dengan pagination — tidak semua dimuat sekaligus.
     */
    public function listComments(int $discussionId, int $perPage = 20): LengthAwarePaginator
    {
        return CommentForum::with('author')
            ->where('discussion_id', $discussionId)
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    public function addComment(int $userId, int $discussionId, string $isiComment): CommentForum
    {
        // Pastikan discussion masih open
        $discussion = Discussion::findOrFail($discussionId);

        if ($discussion->status === Discussion::STATUS_CLOSED) {
            throw new \RuntimeException('Diskusi sudah ditutup, tidak bisa menambahkan komentar.');
        }

        return CommentForum::create([
            'discussion_id' => $discussionId,
            'user_id'       => $userId,
            'isi_comment'   => $isiComment,
        ]);
    }

    public function updateComment(int $commentId, int $userId, string $isiComment): CommentForum
    {
        $comment = CommentForum::findOrFail($commentId);

        // Hanya pemilik yang boleh edit
        if ($comment->user_id !== $userId) {
            throw new \RuntimeException('Tidak memiliki akses untuk mengedit komentar ini.');
        }

        $comment->update(['isi_comment' => $isiComment]);
        return $comment->fresh();
    }

    public function deleteComment(int $commentId, int $userId, bool $isAdmin = false): void
    {
        $comment = CommentForum::findOrFail($commentId);

        if (!$isAdmin && $comment->user_id !== $userId) {
            throw new \RuntimeException('Tidak memiliki akses untuk menghapus komentar ini.');
        }

        $comment->delete();
    }
}