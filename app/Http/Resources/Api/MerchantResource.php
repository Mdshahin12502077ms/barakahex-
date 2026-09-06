<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                             => (int) $this->id,
            'company_name'                   => $this->company,
            'phone'                          => $this->phone_number,
            'account'                        => $this->status,
            'registration_status'            => isset($this->registration_confirmed) && $this->registration_confirmed == '1' ? 'confirmed' : 'not confirmed',
            'website'                        => $this->website,
            'trade_license'                  => $this->trade_license,
            'nid'                            => $this->nid,
            'vat'                            => $this->vat,
            'merchant_cod_charges'           => $this->cod_charges,
            'city'                           => $this->city,
            'address'                        => $this->address,
            'billing_street'                 => $this->billing_street,
            'billing_city'                   => $this->billing_city,
            'billing_postal_code'            => $this->billing_zip,
            'postal_code'                    => $this->zip,
            'created_at'                     => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'                     => $this->updated_at->format('d-m-Y H:i:s'),
        ];

    }
}
