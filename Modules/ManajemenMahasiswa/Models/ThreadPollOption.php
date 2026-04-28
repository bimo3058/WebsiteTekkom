<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreadPollOption extends Model
{
    protected $table = 'mk_thread_poll_options';

    protected $fillable = ['poll_id', 'text', 'votes_count'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(ThreadPoll::class, 'poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ThreadPollVote::class, 'option_id');
    }

    public function percentage(int $total): float
    {
        if ($total === 0) return 0;
        return round(($this->votes_count / $total) * 100, 1);
    }
}
