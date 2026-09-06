<?php

namespace App\Http\Controllers\Api\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Http\Resources\Api\BankResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\MerchantPaymentAccount;
use App\Traits\SendMailTrait;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Interfaces\WithdrawInterface;
class BankController extends Controller
{
    use ApiReturnFormatTrait;
    protected $withdrawRepo;

    public function __construct(WithdrawInterface $withdrawRepo)
    {
        $this->withdrawRepo     = $withdrawRepo;
    }
    public function bank(Request $request)
    {
        try {
            $user = jwtUser();

            if (!$user) {
                return $this->responseWithError('User not authenticated');
            }

            if ($user->user_type == 'merchant') {
                $merchant = Merchant::where('user_id', $user->id)->first();
            } elseif ($user->user_type == 'merchant_staff') {
                $merchant = Merchant::where('id', $user->merchant_id)->first();
            } else {
                return $this->responseWithError('Invalid user type');
            }

            $methods = PaymentMethod::with('payment')->where('type', 'bank')->get();

            $payment = MerchantPaymentAccount::where('merchant_id', $merchant->id)->where('type', 'bank')->first();

            if ($payment) {
                $method = PaymentMethod::where('id', $payment->payment_method_id)->with('payment')->first();
            }

            $data = [
                'all_bank'          => BankResource::collection($methods),
                'bank_branch'       => @$method->payment->bank_branch,
                'bank_ac_name'      => @$method->payment->bank_ac_name,
                'bank_ac_number'    => @$method->payment->bank_ac_number,
                'routing_no'        => @$method->payment->routing_no ,
                'selected_bank_id'  => @$payment->payment_method_id,


            ];

            return $this->responseWithSuccess('Bank info retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }



    public function updateBank(Request $request): \Illuminate\Http\JsonResponse
    {
        $user                      = jwtUser();

        try {
            $this->withdrawRepo->updateBankDetails($request);
            return $this->responseWithSuccess('Bank info updated successfully');

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
