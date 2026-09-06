<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                    => (int) $this->id,
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'email'                 => $this->email,
            'phone_number'          => $this->phone_number,
            'permissions'           => $this->permissions,
            'shops'                 => $this->shops,
            'image'                 => getFileLink('80X80', $this->image_id),
            'created_at'            => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'            => $this->updated_at->format('d-m-Y H:i:s'),
        ];

    }
}
