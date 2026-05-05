<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilReviewRps extends Model
{
    use SoftDeletes;

    protected $table = 'bs_hasil_review_rps';

    protected $fillable = [
        'rps_detail_id',
        'parameter_id',
        'skor',
    ];

    public function rpsDetail()
    {
        return $this->belongsTo(RpsDetail::class, 'rps_detail_id');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id');
    }
}
