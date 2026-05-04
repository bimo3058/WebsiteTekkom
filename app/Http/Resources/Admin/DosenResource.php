<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DosenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'nama'    => $this->nama,
            'email'   => $this->email,
            'nim_nip' => $this->nim_nip,
        ];
    }
}
