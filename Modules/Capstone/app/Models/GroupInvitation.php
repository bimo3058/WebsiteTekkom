<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    protected $table = 'capstone_group_invitations';
    protected $fillable = [
        'group_id',
        'student_id',
        'inviter_id',
        'status', // PENDING, ACCEPTED, REJECTED
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function inviter()
    {
        return $this->belongsTo(Student::class, 'inviter_id');
    }
}
