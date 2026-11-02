<?php

namespace Modules\ManajemenMahasiswa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bidang/Unit tempat seorang pengurus himpunan bertugas. Satu user = satu baris.
 * Diatur lewat form Ubah Role (ManajemenPenggunaController::updateRole).
 */
class PengurusBidang extends Model
{
    protected $table = 'mk_pengurus_bidang';

    protected $fillable = [
        'user_id',
        'bidang_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }
}
