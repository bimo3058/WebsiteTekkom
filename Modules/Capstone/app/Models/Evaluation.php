<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lecturer;
use App\Models\Student;

class Evaluation extends Model
{
    protected $table = 'capstone_evaluations';
    protected $fillable = ['evaluator_id', 'group_id', 'student_id', 'type', 'score', 'feedback'];

    public function evaluator()
    {
        return $this->belongsTo(Lecturer::class, 'evaluator_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
