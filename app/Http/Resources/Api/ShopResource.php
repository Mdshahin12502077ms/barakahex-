<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => (int) $this->id,
            'shop_name'            => $this->shop_name,
            'contact_number'       => $this->contact_number,
            'shop_phone_number'    => $this->shop_phone_number,
            'address'              => $this->address,
            'created_at'           => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'           => $this->updated_at->format('d-m-Y H:i:s'),
        ];

    }
}
