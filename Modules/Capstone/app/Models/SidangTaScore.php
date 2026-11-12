<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Capstone\Traits\HasAssessmentScore;

class SidangTaScore extends Model
{
    use HasAssessmentScore {
        HasAssessmentScore::evaluator as traitEvaluator;
        HasAssessmentScore::scopeByEvaluator as traitScopeByEvaluator;
    }

    protected $table = 'capstone_sidang_ta_scores';

    public $timestamps = true;

    protected $fillable = [
        'component_id',
        'period_component_id',
        'examiner_id',
        'group_id',
        'student_id',
        'score',
        'notes',
        'evaluation_type',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['evaluation_type'];

    public function getEvaluationTypeAttribute(): string
    {
        return 'SIDANG_TA';
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->examiner();
    }

    public function scopeByExaminer($query, int $examinerId)
    {
        return $query->where('examiner_id', $examinerId);
    }

    public function scopeByEvaluator($query, int $evaluatorId)
    {
        return $this->scopeByExaminer($query, $evaluatorId);
    }

    public function getEvaluatorAttribute()
    {
        if (! $this->relationLoaded('examiner')) {
            $this->setRelation('examiner', $this->examiner()->first());
        }

        return $this->relations['examiner'];
    }

    public function isByEvaluator(int $evaluatorId): bool
    {
        return $this->examiner_id === $evaluatorId;
    }
}
