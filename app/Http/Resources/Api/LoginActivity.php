<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginActivity extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => (int) $this->id,
            'browser'         => $this->browser,
            'platform'        => $this->platform,
            'ip'              => $this->ip,
            'time'            => $this->created_at->format('d-m-Y H:i:s'),
            'created_at'      => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'      => $this->updated_at->format('d-m-Y H:i:s'),
        ];

    }
}
