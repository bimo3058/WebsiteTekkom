<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class GroupMember extends Model
{
    protected $table = 'capstone_group_members';
    protected $fillable = ['group_id', 'student_id', 'is_leader', 'period_id'];

    protected $casts = [
        'is_leader' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
