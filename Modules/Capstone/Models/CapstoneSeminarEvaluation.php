<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneSeminarEvaluation extends Model
{
    protected $table = 'capstone_seminar_evaluations';

    protected $fillable = [
        'schedule_id',
        'examiner_id',
        'rubric_json',
        'score',
        'status',
    ];

    protected $casts = [
        'rubric_json' => 'array',
    ];

    const STATUS_PENDING   = 'PENDING';
    const STATUS_SUBMITTED = 'SUBMITTED';

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CapstoneSeminarSchedule::class, 'schedule_id');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_id');
    }
}