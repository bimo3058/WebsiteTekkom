<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportStatusRow extends Model
{
    protected $fillable = [
        'import_status_id', 'row_number', 'email', 'user_id', 'status', 'message',
        'previous_sso_enabled', 'requested_sso_enabled',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'previous_sso_enabled' => 'boolean',
        'requested_sso_enabled' => 'boolean',
    ];

    public function importStatus(): BelongsTo
    {
        return $this->belongsTo(ImportStatus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
