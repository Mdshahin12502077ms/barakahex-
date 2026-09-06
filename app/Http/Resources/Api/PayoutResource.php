<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PayoutResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            'id'            => (int) $this->id,
            'request_id'    => $this->withdraw_id,
            'amount'        => $this->amount,
            'requested_at'  => $this->created_at != '' ? date('M d, Y h:i a', strtotime($this->created_at)) : '',
            'created_at'    => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at'    => $this->updated_at->format('d-m-Y H:i:s'),
        ];


        if (@$this->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::BANK->value) {
            $account_details        = json_decode($this->account_details);
            $data['bank_name']      = @$account_details[0];
            $data['branch']         = @$account_details[1];
            $data['account_holder'] = @$account_details[2];
            $data['account_no']     = @$account_details[3];
            if (@$account_details[4] != '') {
                $data['routing_no'] = @$account_details[4];
            }
        } elseif (@$this->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::MFS->value) {
            $account_details            = json_decode($this->account_details);
            $data['payment_method']     = __(@$account_details[0]);
            $data['account_type']       = __(@$account_details[2]);
            $data['account_holder']     = @$this->user->first_name;
            $data['account_number']     = @$account_details[1];
        } else {
            $data['payment_method']     = __('payment_method').': '. __($this->payment_method_type);
            $data['account_holder']     = @$this->user->first_name;

        }

        $data['status'] = $this->status;
        if (@$this->status == 'rejected' || $this->status == 'cancelled') {
            if ($this->status != 'cancelled' && @$this->companyAccountReason) {
                $data['reject_reason']  = @$this->companyAccountReason->reject_reason != '' ? __($this->companyAccountReason->reject_reason) : '';
            }
            $data['status_updated_at']  = $this->updated_at != '' ? date('M d, Y h:i a', strtotime($this->updated_at)) : '';
        } else {
            $data['transaction_id']     = $this->companyAccount->transaction_id != '' ? $this->companyAccount->transaction_id : '';
            $data['status_updated_at']  = $this->updated_at != '' ? date('M d, Y h:i a', strtotime($this->updated_at)) : '';
        }

        return $data;
    }
}
