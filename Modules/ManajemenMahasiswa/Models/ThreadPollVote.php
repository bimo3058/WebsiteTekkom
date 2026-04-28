<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadPollVote extends Model
{
    protected $table = 'mk_thread_poll_votes';

    protected $fillable = ['poll_id', 'option_id', 'user_id'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(ThreadPoll::class, 'poll_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ThreadPollOption::class, 'option_id');
    }
}
