<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProkerTtd extends Model
{
    protected $table = 'mk_proker_ttd';

    protected $fillable = [
        'kegiatan_id',
        'role',
        'signed_by',
        'signature_image_path',
        'page_number',
        'pos_x_percent',
        'pos_y_percent',
        'width_percent',
        'height_percent',
        'signed_at',
    ];

    protected $casts = [
        'signed_at'       => 'datetime',
        'pos_x_percent'   => 'float',
        'pos_y_percent'   => 'float',
        'width_percent'   => 'float',
        'height_percent'  => 'float',
        'page_number'     => 'integer',
    ];

    // ── Role constants ────────────────────────────────────────────────────────

    const ROLE_KETUA_HIMPUNAN  = 'ketua_himpunan';
    const ROLE_BENDAHARA       = 'bendahara';
    const ROLE_DPM             = 'dpm';
    const ROLE_KETUA_DEPT      = 'ketua_departemen';

    const ROLE_LABELS = [
        self::ROLE_KETUA_HIMPUNAN => 'Ketua Himpunan',
        self::ROLE_BENDAHARA      => 'Bendahara',
        self::ROLE_DPM            => 'DPM',
        self::ROLE_KETUA_DEPT     => 'Ketua Departemen',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'signed_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_image_path
            ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->signature_image_path)
            : null;
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst($this->role);
    }
}
