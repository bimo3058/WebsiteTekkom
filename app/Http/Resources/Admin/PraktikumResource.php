<?php

namespace App\Http\Resources\Admin;

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
            'dosen'        => $this->whenLoaded('dosen', function () {
                return [
                    'id'   => $this->dosen->id,
                    'nama' => $this->dosen->nama,
                ];
            }),
            'koordinator'  => $this->whenLoaded('koordinator', function () {
                return [
                    'id'   => $this->koordinator->id,
                    'nama' => $this->koordinator->nama,
                ];
            }),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
