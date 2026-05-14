<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DaftarPraktikan extends Model
{
    use HasUuids;

    protected $table = 'daftar_praktikan';

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'status',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
