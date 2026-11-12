<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    protected $table = 'capstone_document_versions';

    protected $fillable = [
        'document_id',
        'version',
        'file_path',
        'original_name',
        'created_by',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
