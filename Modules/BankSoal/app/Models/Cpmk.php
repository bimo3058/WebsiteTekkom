<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    protected $table    = 'bs_cpmk';
    protected $fillable = ['kode', 'deskripsi', 'mk_id', 'cpl_id'];
    public $timestamps = true;

    public function getDeskripsiAttribute($value)
    {
        $value = $value ?? '';
        
        if (preg_match('/^\((.*?)\)\s+\((.*?)\)(?:\s+\((.*?)\))?$/', $value, $matches)) {
            $kko = trim($matches[1]);
            $objek = trim($matches[2]);
            $konteks = isset($matches[3]) ? trim($matches[3]) : '';
            
            $parts = ['Mahasiswa mampu', $kko, $objek];
            if ($konteks !== '') {
                $parts[] = $konteks;
            }
            return implode(' ', $parts);
        }
        
        return $value;
    }

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'bs_cpl_cpmk', 'cpmk_id', 'cpl_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'cpl_id');
    }
}
