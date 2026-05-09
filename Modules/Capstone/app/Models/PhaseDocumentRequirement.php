<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class PhaseDocumentRequirement extends Model
{
    protected $table = 'capstone_phase_document_requirements';
    protected $fillable = [
        'period_id',
        'phase',
        'name',
        'description',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
