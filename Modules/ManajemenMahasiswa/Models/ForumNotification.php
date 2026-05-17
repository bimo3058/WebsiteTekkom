<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumNotification extends Model
{
    protected $table = 'mk_forum_notifications';

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'thread_id',
        'comment_id',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'actor_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Create a notification for a reply to a comment.
     */
    public static function notifyCommentReply(Comment $comment, Thread $thread): void
    {
        $actorId = $comment->user_id;

        // 1. Notify parent comment author (if replying to someone else's comment)
        if ($comment->parent_id) {
            $parent = Comment::find($comment->parent_id);
            if ($parent && $parent->user_id !== $actorId) {
                $actorName = \App\Models\User::find($actorId)?->name ?? 'Seseorang';
                static::create([
                    'user_id'    => $parent->user_id,
                    'actor_id'   => $actorId,
                    'type'       => 'reply_comment',
                    'thread_id'  => $thread->id,
                    'comment_id' => $comment->id,
                    'message'    => "{$actorName} membalas komentar Anda di \"{$thread->judul}\"",
                ]);
            }
        }

        // 2. Notify thread author (if someone else comments on their thread)
        if ($thread->user_id !== $actorId) {
            // Don't send duplicate if already notified as parent comment author
            $alreadyNotified = $comment->parent_id
                && ($parent ?? null)
                && $parent->user_id === $thread->user_id;

            if (!$alreadyNotified) {
                $actorName = \App\Models\User::find($actorId)?->name ?? 'Seseorang';
                static::create([
                    'user_id'    => $thread->user_id,
                    'actor_id'   => $actorId,
                    'type'       => 'reply_thread',
                    'thread_id'  => $thread->id,
                    'comment_id' => $comment->id,
                    'message'    => "{$actorName} berkomentar di thread Anda \"{$thread->judul}\"",
                ]);
            }
        }
    }
}
