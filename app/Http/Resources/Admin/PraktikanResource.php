<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PraktikanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'      => $this->id, // UUID Pengguna
            'nim'     => $this->nim_nip,
            'nama'    => $this->nama,
            'email'   => $this->email,
            'status'  => $this->pivot->status ?? 'terdaftar',
            'terdaftar_pada' => $this->pivot->created_at ?? null,
        ];
    }
}
