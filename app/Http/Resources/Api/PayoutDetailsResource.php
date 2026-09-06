<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PayoutDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        $withdraw = $this->withdraw;

        $data = [
            'id'         => (int) $this->id,
            'withdraw_id' => $this->withdraw_id,
            'merchant'   => $this->merchant->company ?? null,
            'phone'      => $this->merchant->phone_number ?? null,
            'address'    => $this->merchant->address ?? null,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
        ];

        if ($this->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::BANK->value) {
            $account_details = json_decode($this->account_details);
            $data['bank_name'] = @$account_details[0];
            $data['branch'] = @$account_details[1];
            $data['account_holder'] = @$account_details[2];
            $data['account_no'] = @$account_details[3];
            if (@$account_details[4] != '') {
                $data['routing_no'] = @$account_details[4];
            }
        } elseif ($this->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::MFS->value) {
            $account_details = json_decode($this->account_details);
            $data['payment_method'] = __(@$account_details[0]);
            $data['account_type'] = __(@$account_details[2]);
            $data['account_holder'] = @$this->user->first_name;
            $data['account_number'] = @$account_details[1];
        } else {
            $data['payment_method'] = __('payment_method').': '. __($this->payment_method_type);
            $data['account_holder'] = @$this->user->first_name;

        }
        $data['amount'] = $this->amount;

        $data['note'] = $this->note;


        $tableData = [];

        foreach ($this->merchantAccounts ?? [] as $key => $merchant_account) {
            $rowData = [
                'source' => __($merchant_account->source),
                'parcel_no' => @$merchant_account->parcel->parcel_no,
                'details' => __($merchant_account->details),
                'created_at' => $merchant_account->created_at != '' ? date('M d, Y h:i a', strtotime($merchant_account->created_at)) : '',
                'transaction_type' => $merchant_account->type == 'income' ? __('credit') : __('debit'),
                'amount' => $merchant_account->amount,
            ];
            $tableData[] = $rowData;
        }

        return array_merge($data, [
            'debit_credit' => $tableData,
        ]);
    }
}
