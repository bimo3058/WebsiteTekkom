<?php

namespace Modules\BankSoal\Models\Shared;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\BankSoal\Models\RpsDetail;

class Rps extends Model
{
    protected $table    = 'bs_rps';
    protected $fillable = ['mk_id', 'dosen_id', 'tenggat', 'semester_berlaku'];

    protected function casts(): array
    {
        return ['tenggat' => 'datetime'];
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function detail()
    {
        return $this->hasOne(RpsDetail::class, 'rps_id');
    }
}
