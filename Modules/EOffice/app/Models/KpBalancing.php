<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpBalancing extends Model
{
    use HasFactory;

    protected $table = 'eo_kp_balancing';

    protected $fillable = [
        'kp_id',
        'mahasiswa_id',
        'dosen_id',
        'status',
        'assigned_by',
        'assigned_at',
        'finalized_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'kp_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(KpMahasiswa::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(KpDosen::class, 'dosen_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
