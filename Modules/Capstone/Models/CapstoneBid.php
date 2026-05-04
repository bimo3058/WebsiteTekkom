<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneBid extends Model
{
    protected $table = 'capstone_bids';

    protected $fillable = [
        'group_id',
        'title_id',
        'priority',
        'status',
        'lecturer_recommendation',
        'proposed_supervisor_1_id',
        'proposed_supervisor_2_id',
    ];

    const STATUS_PENDING  = 'PENDING';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_REJECTED = 'REJECTED';

    public function group(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'group_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(CapstoneTitle::class, 'title_id');
    }

    public function proposedSupervisor1(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'proposed_supervisor_1_id');
    }

    public function proposedSupervisor2(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'proposed_supervisor_2_id');
    }
}