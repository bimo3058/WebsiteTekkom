<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaResource extends JsonResource
{
    public function toArray($request): array
    {
        // Load roles jika belum di-load
        $roles      = $this->roles ?? collect();
        $roleNames  = $roles->pluck('nama')->toArray();

        return [
            'id'             => $this->id,
            'nama'           => $this->nama,
            'email'          => $this->email,
            'nim_nip'        => $this->nim_nip,
            'status'         => $this->status,
            'roles'          => $roleNames,
            'role_tertinggi' => $this->roleTertinggi(),
        ];
    }
}
