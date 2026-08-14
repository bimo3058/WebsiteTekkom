<?php

namespace Modules\Capstone\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    protected $table = 'capstone_digital_signatures';
    protected $fillable = [
        'user_id',
        'document_reference',
        'document_type',
        'signature_data',
        'hash',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
