<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class ExpoSelfEvaluation extends Model
{
    protected $table = 'capstone_expo_self_evaluations';
    protected $fillable = ['expo_registration_id', 'group_id', 'student_id', 'period_component_id', 'score', 'notes'];
    protected $casts = ['score' => 'decimal:2'];

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class);
    }
}
