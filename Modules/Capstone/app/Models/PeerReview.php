<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class PeerReview extends Model
{
    protected $table = 'capstone_peer_reviews';
    protected $fillable = [
        'group_id',
        'reviewer_id',
        'reviewee_id',
        'indicator_id',
        'period_indicator_id',
        'score',
        'comment',
        'raw_score',
        'is_final_submission',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'raw_score' => 'decimal:2',
        'is_final_submission' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Student::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(Student::class, 'reviewee_id');
    }

    public function indicator()
    {
        return $this->belongsTo(PeerReviewIndicator::class, 'indicator_id');
    }

    public function periodIndicator()
    {
        return $this->belongsTo(PeriodPeerReviewIndicator::class, 'period_indicator_id');
    }
}
