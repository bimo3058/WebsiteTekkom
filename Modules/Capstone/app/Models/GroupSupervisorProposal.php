<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;

class GroupSupervisorProposal extends Model
{
    protected $table = 'capstone_group_supervisor_proposals';
    protected $fillable = [
        'group_id',
        'proposed_supervisor_1_id',
        'proposed_supervisor_2_id',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function supervisor1()
    {
        return $this->belongsTo(Lecturer::class, 'proposed_supervisor_1_id');
    }

    public function supervisor2()
    {
        return $this->belongsTo(Lecturer::class, 'proposed_supervisor_2_id');
    }
}
