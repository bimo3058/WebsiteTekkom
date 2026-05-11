<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class PeerReviewIndicator extends Model
{
    protected $table = 'capstone_peer_review_indicators';
    protected $fillable = [
        'period_id',
        'name',
        'description',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function reviews()
    {
        return $this->hasMany(PeerReview::class, 'indicator_id');
    }
}
