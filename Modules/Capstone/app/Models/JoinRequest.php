<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    protected $table = 'capstone_join_requests';
    protected $fillable = [
        'group_id',
        'requester_id',
        'status', // PENDING, ACCEPTED, REJECTED, INVALIDATED
        'message',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function requester()
    {
        return $this->belongsTo(Student::class, 'requester_id');
    }
}
