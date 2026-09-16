<?php

namespace Modules\Capstone\Models;

use Modules\Capstone\Traits\HasAssessmentScore;
use Illuminate\Database\Eloquent\Model;

/**
 * Milestone Score Model
 *
 * Stores assessment scores for MILESTONE evaluation type.
 * This model replaces evaluation_type='MILESTONE' records from the old assessment_scores table.
 */
class MilestoneScore extends Model
{
    protected $table = 'capstone_milestone_scores';
    use HasAssessmentScore;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'component_id',
        'period_component_id',
        'evaluator_id',
        'group_id',
        'student_id',
        'score',
        'notes',
        'evaluation_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'decimal:2',
        'notes' => 'string',
    ];

    protected $appends = ['evaluation_type'];

    public function getEvaluationTypeAttribute(): string
    {
        return 'MILESTONE';
    }
}
