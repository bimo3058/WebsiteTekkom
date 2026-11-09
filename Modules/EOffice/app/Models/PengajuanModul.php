<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PengajuanModul extends Model
{
    protected $table = 'pengajuan_modul';

    protected $fillable = [
        'praktikum_id',
        'praktikum_sumber_id',
        'versi',
        'status',
        'diajukan_oleh',
        'direview_oleh',
        'catatan_pengajuan',
        'catatan_reviewer',
        'diajukan_at',
        'direview_at',
    ];

    protected function casts(): array
    {
        return [
            'diajukan_at' => 'datetime',
            'direview_at' => 'datetime',
        ];
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function praktikumSumber()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_sumber_id');
    }

    public function items()
    {
        return $this->hasMany(PengajuanModulItem::class, 'pengajuan_modul_id')->orderBy('urutan_baru');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'direview_oleh');
    }

    public function dapatDiedit(): bool
    {
        return in_array($this->status, ['draft', 'perlu_revisi'], true);
    }
}
