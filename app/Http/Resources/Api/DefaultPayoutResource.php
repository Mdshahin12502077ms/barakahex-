<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DefaultPayoutResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => @$this->paymentAccount->name,
            'selected' => $this->id == @$this->merchant->default_account_id,
        ];
    }
}


