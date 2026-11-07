<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TugasAkhir extends Model
{
    use HasUuids;
    protected $table = 'eo_prak_tugas_akhir';
    protected $guarded = [];
    protected $casts = ['tahap' => 'array', 'komponen' => 'array', 'lampiran' => 'array', 'riwayat' => 'array', 'published' => 'boolean'];

    public function componentsFor(string $stageId): array
    {
        // Legacy shared components remain on the first stage; identifiers and scores are preserved.
        $first = $this->tahap[0]['id'] ?? null;
        return collect($this->komponen ?? [])->filter(fn($c) => ($c['tahap_id'] ?? $first) === $stageId)->values()->all();
    }

    public function validWeights(): bool
    {
        return count($this->komponen ?? [])>0 && abs(collect($this->komponen)->sum('bobot')-100)<.001
            && collect($this->komponen)->every(fn($c)=>is_numeric($c['bobot']??null) && (float)$c['bobot']===(float)(int)$c['bobot'] && $c['bobot']>0 && $c['bobot']<=100);
    }

    public function average(array $scores, ?string $stageId=null): ?float
    {
        if (!$this->validWeights()) return null;
        $components=$stageId ? $this->componentsFor($stageId) : $this->komponen;
        $total=collect($components)->sum('bobot');
        if (!$total) return null;
        return collect($components)->sum(fn($c)=>(float)($scores[$c['id']]??0)*(float)$c['bobot'])/$total;
    }
}
