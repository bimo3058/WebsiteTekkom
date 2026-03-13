<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Discussion extends Model
{
    use HasFactory;

    protected $table = 'mk_discussion';

    protected $fillable = [
        'forum_id',
        'user_id',
        'judul_discussion',
        'isi_discussion',
        'status',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_OPEN   = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_PINNED = 'pinned';

    const STATUS_LIST = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
        self::STATUS_PINNED,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function forum(): BelongsTo
    {
        return $this->belongsTo(ForumMahasiswa::class, 'forum_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CommentForum::class, 'discussion_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PINNED);
    }

    public function scopeByForum(Builder $query, int $forumId): Builder
    {
        return $query->where('forum_id', $forumId);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where('judul_discussion', 'like', "%{$keyword}%")
                     ->orWhere('isi_discussion', 'like', "%{$keyword}%");
    }
}