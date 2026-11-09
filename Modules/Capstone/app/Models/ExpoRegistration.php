<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class ExpoRegistration extends Model
{
    protected $table = 'capstone_expo_registrations';
    protected $fillable = [
        'expo_event_id',
        'group_id',
        'registered_at',
        'status',
        'review_status',
        'review_feedback',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Whether the representative member may still (re)submit the group's
     * self-evaluation and document. A rejected submission is reopened;
     * submitted and approved ones are locked.
     */
    public function allowsSubmission(): bool
    {
        return $this->review_status === null || $this->review_status === 'REJECTED';
    }

    public function expoEvent()
    {
        return $this->belongsTo(ExpoEvent::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
