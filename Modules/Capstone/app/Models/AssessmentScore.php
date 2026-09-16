<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    protected $table = 'capstone_assessment_scores';
    protected $fillable = [
        'component_id',
        'evaluator_id',
        'group_id',
        'student_id',
        'score',
        'notes',
        'evaluation_type',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function component()
    {
        return $this->belongsTo(AssessmentComponent::class, 'component_id');
    }

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
