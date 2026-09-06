<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ParcelResource extends JsonResource
{
    public function toArray($request): array
    {
        if($this->packaging != 'no') {
            $packagingData 	= settingHelper('package_and_charges');
            $packaging 		= collect($packagingData)->firstWhere('id', $this->packaging);
        }

        return [
            'id'                      => (int) $this->id,
            'parcel_no'               => $this->parcel_no,
            'customer_name'           => $this->customer_name,
            'customer_phone_number'   => $this->customer_phone_number,
            'customer_address'        => $this->customer_address,
            'status'                  => $this->status,
            'cod_charge'              => $this->cod_charge,
            'shop_name'               => @$this->shop->shop_name,
            'invoice'                 => $this->customer_invoice_no	,
            'weight'                  => $this->weight,
            'cod'                     => $this->price,
            'selling_price'           => $this->selling_price,
            'delivery_area'           => $this->parcel_type,
            'pickup_number'           => $this->pickup_shop_phone_number,
            'pickup_address'          => $this->pickup_address,
            'note'                    => $this->note,
            'fragile'                 => $this->fragile,
            'fragile_charge'          => settingHelper('fragile_charge'),
            'packaging'               => $packaging->package_type ?? null,
            'packaging_charge'        => $packaging->charge ?? null,
            'created_at'              => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'              => $this->updated_at->format('d-m-Y H:i:s'),
        ];

    }
}
