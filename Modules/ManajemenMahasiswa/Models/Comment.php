<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'mk_comments';

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'konten',
        'is_best_answer',
        'vote_count',
    ];

    protected function casts(): array
    {
        return [
            'is_best_answer' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Recursive: all nested replies (for Reddit-style threading).
     */
    public function allReplies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with(['allReplies.author', 'parent.author']);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function syncVoteCount(): void
    {
        $this->update([
            'vote_count' => $this->votes()->where('value', 1)->count(),
        ]);
    }

    /**
     * Get all nested replies flattened into a single collection, sorted by creation date.
     */
    public function getFlattenedReplies(): \Illuminate\Support\Collection
    {
        $flattened = collect();

        $traverse = function ($replies) use (&$traverse, &$flattened) {
            foreach ($replies as $reply) {
                $flattened->push($reply);
                if ($reply->allReplies && $reply->allReplies->isNotEmpty()) {
                    $traverse($reply->allReplies);
                }
            }
        };

        if ($this->allReplies) {
            $traverse($this->allReplies);
        }

        return $flattened->sortBy('created_at')->values();
    }

    /**
     * Get the username of the parent comment's author, if this is a reply to a nested comment.
     * Returns null if this is a reply directly to a top-level comment.
     */
    public function getRepliedToUsername(): ?string
    {
        if (!$this->parent_id) {
            return null; // This is a top-level comment
        }

        $parent = $this->parent;
        
        // If the parent has no parent, it's a top-level comment.
        // In YouTube style, we only explicitly mention the user if replying to a reply.
        if (!$parent || !$parent->parent_id) {
            return null;
        }

        return $parent->author->name ?? 'Unknown';
    }
}
