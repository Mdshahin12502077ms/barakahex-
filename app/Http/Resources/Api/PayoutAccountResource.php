<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PayoutAccountResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            'id'            => (int) $this->id,
            'name'          => $this->paymentAccount->name ?? '',
        ];

        return $data;
    }
}
