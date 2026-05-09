<?php

namespace Modules\EOffice\Http\Resources\ManajemenPraktikum\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DosenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,   // User global pakai 'name'
            'email' => $this->email,
        ];
    }
}
