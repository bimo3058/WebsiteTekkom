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
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'decimal:2',
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
}
