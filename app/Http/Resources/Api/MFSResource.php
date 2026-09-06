<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MFSResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'image'     => getFileLink('original_image', $this->image),
            'number'    => $this->payment->mfs_number ?? null,
            'type'      => $this->payment->mfs_ac_type ?? null,
        ];
    }
}



