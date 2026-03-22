<?php

namespace Modules\BankSoal\Models\Shared;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;

class DosenPengampuMk extends Model
{
    protected $table    = 'bs_dosen_pengampu_mk';
    protected $fillable = ['mk_id', 'dosenpengampu_id'];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'dosenpengampu_id');
    }
}
