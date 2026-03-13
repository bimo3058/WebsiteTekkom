<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapstoneTitle extends Model
{
    use SoftDeletes;

    protected $table = 'capstone_titles';

    protected $fillable = [
        'lecturer_id',
        'title',
        'description',
        'problem_statement',
        'scope',
        'specializations',
        'quota',
        'status',
        'approved_by_admin',
        'title_source',
        'proposed_by_group_id',
        'proposed_supervisor_id',
        'supervisor_approval_status',
    ];

    protected $casts = [
        'specializations'  => 'array',
        'approved_by_admin' => 'boolean',
    ];

    const STATUS_OPEN   = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    const SOURCE_LECTURER = 'LECTURER';
    const SOURCE_STUDENT  = 'STUDENT';

    const APPROVAL_PENDING  = 'PENDING';
    const APPROVAL_APPROVED = 'APPROVED';
    const APPROVAL_REJECTED = 'REJECTED';

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }

    public function proposedByGroup(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'proposed_by_group_id');
    }

    public function proposedSupervisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'proposed_supervisor_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(CapstoneGroup::class, 'title_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(CapstoneBid::class, 'title_id');
    }
}