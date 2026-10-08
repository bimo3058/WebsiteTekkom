<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMember extends Model
{
    use SoftDeletes;

    protected $table = 'capstone_group_members';

    protected $fillable = ['group_id', 'student_id', 'is_leader', 'period_id', 'status', 'removed_by', 'removal_reason'];

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
