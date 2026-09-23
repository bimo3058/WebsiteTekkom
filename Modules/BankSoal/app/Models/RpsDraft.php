<?php

namespace Modules\BankSoal\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RpsDraft extends Model
{
    protected $table = 'bs_rps_draft';

    protected $fillable = [
        'user_id', 'mk_id', 'semester', 'tahun_ajaran',
        'creation_method', 'step_reached',
        'dosen_lain', 'data_step_1', 'data_step_2', 'data_step_3',
    ];

    protected $casts = [
        'dosen_lain'   => 'array',
        'data_step_1'  => 'array',
        'data_step_2'  => 'array',
        'data_step_3'  => 'array',
        'step_reached' => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }
}
