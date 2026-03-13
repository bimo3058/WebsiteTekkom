<?php

namespace Modules\Capstone\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneDocument extends Model
{
    protected $table = 'capstone_documents';

    protected $fillable = [
        'group_id',
        'uploaded_by',
        'phase',
        'document_type',
        'file_path',
        'status',
        'reviewed_by',
        'feedback',
    ];

    const STATUS_PENDING  = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    const PHASE_PDC1 = 'PDC1';
    const PHASE_PDC2 = 'PDC2';

    public function group(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'group_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}