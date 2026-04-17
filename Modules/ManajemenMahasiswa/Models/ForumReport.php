<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumReport extends Model
{
    protected $table = 'mk_forum_reports';

    protected $fillable = [
        'user_id',
        'thread_id',
        'alasan',
        'status',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }
}
