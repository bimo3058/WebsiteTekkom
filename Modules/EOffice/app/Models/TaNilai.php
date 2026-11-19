<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaNilai extends Model
{
    use HasUuids;
    protected $table = 'eo_prak_ta_nilai';
    protected $guarded = [];
    protected $casts = ['skor' => 'array', 'riwayat' => 'array', 'disetujui_koor' => 'boolean', 'disetujui_dosen' => 'boolean'];

    public function initialScores(): array
    {
        $entry = collect($this->riwayat ?? [])->last(fn($e) => isset($e['awal']));
        return $entry['awal'] ?? $this->skor ?? [];
    }

    public function revisionScores(): array
    {
        $entry = collect($this->riwayat ?? [])->last(fn($e) => isset($e['awal']));
        return $entry['revisi'] ?? [];
    }
}
