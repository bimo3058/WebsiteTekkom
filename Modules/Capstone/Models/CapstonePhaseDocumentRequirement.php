<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstonePhaseDocumentRequirement extends Model
{
    protected $table = 'capstone_phase_document_requirements';

    protected $fillable = [
        'period_id',
        'phase',
        'document_type',
        'name',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    const PHASE_PDC1   = 'PDC1';
    const PHASE_SEMPRO = 'SEMPRO';
    const PHASE_PDC2   = 'PDC2';
    const PHASE_TA     = 'TA';

    public function period(): BelongsTo
    {
        return $this->belongsTo(CapstonePeriod::class, 'period_id');
    }
}