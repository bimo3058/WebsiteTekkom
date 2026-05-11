<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'capstone_document_types';
    protected $fillable = [
        'name',
        'description',
        'phase',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
