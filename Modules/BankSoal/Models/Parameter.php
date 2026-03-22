<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parameter extends Model
{
    use HasFactory;

    protected $table = 'bs_parameter';

    protected $fillable = [
        'aspek',
        'bobot',
    ];

    protected $casts = [
        'bobot' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function hasilReview(): HasMany
    {
        return $this->hasMany(HasilReviewRps::class, 'parameter_id');
    }
}