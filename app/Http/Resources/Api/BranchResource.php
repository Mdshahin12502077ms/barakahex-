<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => (int) $this->id,
            'name'                 => $this->name,
            'created_at'           => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'           => $this->updated_at->format('d-m-Y H:i:s'),
        ];

    }
}
