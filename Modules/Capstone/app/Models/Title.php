<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
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
        'rejection_reason',
        'period_id',
        'pre_assigned_group_id',
        'is_reserved',
    ];

    protected $casts = [
        'specializations' => 'array',
        'is_reserved' => 'boolean',
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function proposedByGroup()
    {
        return $this->belongsTo(Group::class, 'proposed_by_group_id');
    }

    public function proposedSupervisor()
    {
        return $this->belongsTo(Lecturer::class, 'proposed_supervisor_id');
    }
}
