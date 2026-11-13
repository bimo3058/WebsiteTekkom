<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaPengumpulan extends Model
{
    use HasUuids;
    protected $table = 'eo_prak_ta_pengumpulan';
    protected $guarded = [];
    protected $casts = ['riwayat' => 'array', 'deadline_revisi' => 'datetime'];

    public function entries(): \Illuminate\Support\Collection
    {
        return collect($this->riwayat ?? [])->filter(fn($entry)=>($entry['jenis']??null)==='pengumpulan' && empty($entry['dibatalkan_pada']))->values();
    }

    public function token(): string
    {
        return hash('sha256',json_encode($this->riwayat ?? []));
    }
}
