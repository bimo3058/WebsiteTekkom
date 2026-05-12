<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsprakPraktikumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
                'external_id' => $this->user->external_id ?? null,
            ],
            'role' => $this->role,
            'deskripsi' => $this->deskripsi,
            'assigned_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
