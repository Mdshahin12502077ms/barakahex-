<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\PayoutDetailsResource;
use App\Repositories\Interfaces\WithdrawInterface;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\Auth;

class PayoutDetailsController extends Controller
{
    use ApiReturnFormatTrait;

    protected $payoutRepo;

    public function __construct(WithdrawInterface $payoutRepo)
    {
        $this->payoutRepo = $payoutRepo;
    }

    public function payoutDetails($id): \Illuminate\Http\JsonResponse
    {
        try {

            $user = Auth::user();
            $payoutLogs = $this->payoutRepo->get($id);



                $data = [
                    'id'          => (int) $payoutLogs->id,
                    'withdraw_id' => $payoutLogs->withdraw_id,
                    'merchant'    => $payoutLogs->merchant->company ?? null,
                    'phone'       => $payoutLogs->merchant->phone_number ?? null,
                    'address'     => $payoutLogs->merchant->address ?? null,
                    'created_at'  => $payoutLogs->created_at->format('d-m-Y H:i:s'),
                    'updated_at'  => $payoutLogs->updated_at->format('d-m-Y H:i:s'),
                ];

                if (@$payoutLogs->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::BANK->value) {
                    $account_details = json_decode($payoutLogs->account_details);
                    $data['bank_name'] = @$account_details[0];
                    $data['branch'] = @$account_details[1];
                    $data['account_holder'] = @$account_details[2];
                    $data['account_no'] = @$account_details[3];
                    if (@$account_details[4] != '') {
                        $data['routing_no'] = @$account_details[4];
                    }
                } elseif (@$payoutLogs->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::MFS->value) {
                    $account_details = json_decode($payoutLogs->account_details);
                    $data['payment_method'] = __(@$account_details[0]);
                    $data['account_type'] = __(@$account_details[2]);
                    $data['account_holder'] = @$payoutLogs->user->first_name;
                    $data['account_number'] = @$account_details[1];
                } else {
                    $data['payment_method'] = __('payment_method').': '. __($payoutLogs->payment_method_type);
                    $data['account_holder'] = @$payoutLogs->user->first_name;

                }
                $data['amount'] = $payoutLogs->amount;

                $data['note'] = $payoutLogs->note;


                $tableData = [];

                foreach ($payoutLogs->merchantAccounts ?? [] as $key => $merchant_account) {
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

                $data['debit_credit'] = $tableData;

            return $this->responseWithSuccess('payout_details_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }
}
