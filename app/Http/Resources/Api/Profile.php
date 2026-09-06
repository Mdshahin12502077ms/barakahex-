<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class Profile extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => (int) $this->id,
            'name'            => $this->first_name .' '.$this->last_name,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'email'           => $this->email,
            'merchant'        => $this->merchant->company ?? $this->staffMerchant->company,
            'image'           => $this->image ? asset($this->image->image_small_two) : '',
            'created_at'      => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'      => $this->updated_at->format('d-m-Y H:i:s'),
            'address'         => $this->merchant ? $this->merchant->address : null,
        ];
    }
}

