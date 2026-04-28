<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ThreadPoll extends Model
{
    protected $table = 'mk_thread_polls';

    protected $fillable = ['thread_id', 'expires_at', 'is_closed'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_closed'  => 'boolean',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ThreadPollOption::class, 'poll_id')->orderBy('id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ThreadPollVote::class, 'poll_id');
    }

    public function isClosed(): bool
    {
        if ($this->is_closed) return true;
        if ($this->expires_at && $this->expires_at->isPast()) return true;
        return false;
    }

    public function totalVotes(): int
    {
        return $this->options->sum('votes_count');
    }

    public function userVote(): ?ThreadPollVote
    {
        if (!Auth::check()) return null;
        return $this->votes->firstWhere('user_id', Auth::id());
    }

    public function userVotedOptionId(): ?int
    {
        return $this->userVote()?->option_id;
    }
}
