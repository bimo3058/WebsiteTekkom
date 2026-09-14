<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Supervision extends Model
{
    protected $table = 'capstone_supervisions';
    protected $fillable = [
        'group_id',
        'supervisor_id',
        'role',
        'assigned_by',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
