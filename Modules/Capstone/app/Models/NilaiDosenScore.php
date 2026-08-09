<?php

namespace Modules\Capstone\Models;

use Modules\Capstone\Traits\HasAssessmentScore;
use Illuminate\Database\Eloquent\Model;

/**
 * Nilai Dosen Score Model
 *
 * Stores assessment scores for NILAI_DOSEN evaluation type.
 * This model replaces evaluation_type='NILAI_DOSEN' records from the old assessment_scores table.
 */
class NilaiDosenScore extends Model
{
    protected $table = 'capstone_nilai_dosen_scores';
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
        return 'NILAI_DOSEN';
    }
}
