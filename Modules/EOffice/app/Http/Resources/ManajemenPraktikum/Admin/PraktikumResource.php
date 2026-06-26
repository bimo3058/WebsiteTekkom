<?php

namespace Modules\EOffice\Http\Resources\ManajemenPraktikum\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PraktikumResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'nama'         => $this->nama,
            'kode'         => $this->kode,
            'deskripsi'    => $this->deskripsi,
            'tahun_ajaran' => $this->tahun_ajaran,
            'semester'     => $this->semester,
            'status'       => $this->status,
            'created_at'   => $this->created_at?->toISOString(),

            // Relasi ke User global — field 'name' bukan 'nama'
            'dosens' => $this->whenLoaded('dosens', fn() => $this->dosens->map(fn($d) => [
                'id'    => $d->id,
                'name'  => $d->name,
                'email' => $d->email,
            ])),
            'koordinator' => $this->whenLoaded('koordinator', fn() => [
                'id'    => $this->koordinator->id,
                'name'  => $this->koordinator->name,
                'email' => $this->koordinator->email,
            ]),
        ];
    }
}
