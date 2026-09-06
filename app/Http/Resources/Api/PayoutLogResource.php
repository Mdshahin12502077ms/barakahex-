<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PayoutLogResource extends JsonResource
{


    public function toArray($request): array
    {
        $details = __($this->details) . "\n";
        if (@$this->parcel != '') {
            $details .= __('id') . ":#" . __(@$this->parcel->parcel_no) . "\n";
        }

        if (@$this->parcel->customer_invoice_no != '') {
            $details .= __('invno') . ":#" . __($this->parcel->customer_invoice_no);
        }

        $amount = '';
        if ($this->type == 'income') {
            $amount = $this->amount;
        } elseif ($this->type == 'expense') {
            $amount = $this->amount;
        }



        return [
            'id'         => (int) $this->id,
            'details'    => $details,
            'amount'     => $amount,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
        ];
    }
}
