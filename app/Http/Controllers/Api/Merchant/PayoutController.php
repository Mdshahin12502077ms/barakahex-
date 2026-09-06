<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Account\DeliveryManAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Shop;
use App\Models\User;
use App\Models\MerchantPaymentAccount;
use App\Models\Merchant;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\MerchantBalanceTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Api\PayoutResource;
use App\Http\Resources\Api\PayoutAccountResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendMailTrait;
use App\Repositories\Interfaces\WithdrawInterface;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class PayoutController extends Controller
{
    use ApiReturnFormatTrait, MerchantBalanceTrait;

    protected $withdrawRepo;

    public function __construct(WithdrawInterface $withdrawRepo)
    {

        $this->withdrawRepo     = $withdrawRepo;

    }
    public function allPayout(): \Illuminate\Http\JsonResponse
    {
        try {

            $user = jwtUser();
            $query = MerchantWithdraw::query();
            $userPermissions = $user->permissions;

            if ($user->user_type == 'merchant_staff') {
                $query->where('merchant_id', $user->merchant_id)
                    ->when(is_array($userPermissions) && !in_array('all_parcel_payment', $userPermissions), function ($query) use ($user) {
                        $query->whereHas('companyAccount', function ($q) use ($user) {
                            $q->where('created_by', $user->id);
                        });
                    });
            }

            if ($user->user_type == 'merchant') {
                $query->where('merchant_id', $user->merchant->id);
            }

            $payouts = $query->latest()->paginate();
            $data = [
                'payouts' => PayoutResource::collection($payouts),
                'paginate' => [
                    'total' => $payouts->total(),
                    'current_page'  => $payouts->currentPage(),
                    'per_page'      => $payouts->perPage(),
                    'last_page'     => $payouts->lastPage(),
                    'prev_page_url' => $payouts->previousPageUrl(),
                    'next_page_url' => $payouts->nextPageUrl(),
                    'path'          => $payouts->path(),
                ],
            ];

            return $this->responseWithSuccess('payouts_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function account(): \Illuminate\Http\JsonResponse
    {
        try {

            $user = jwtUser();

            if ($user->user_type == 'merchant') {
                $merchant = Merchant::where('user_id', $user->id)->first();
            }
            if ($user->user_type == 'merchant_staff') {
                $merchant = Merchant::where('id', $user->merchant_id)->first();
            }
            $payment_account        = MerchantPaymentAccount::where('merchant_id', $merchant->id)->groupBy('payment_method_id')->with('paymentAccount')->get();

            $data = [
                'accounts'          => PayoutAccountResource::collection($payment_account),
            ];

            return $this->responseWithSuccess('payout_account_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function submitPayout(Request $request, $id = null): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        $validator = Validator::make($request->all(), [
            'amount'        => 'required|min:1',
            'withdraw_to'   => 'required',
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->amount < 1) {
            return response()->json(['error' => 'amount_must_be greater_than 0'], 422);
        }

        try {

            $users                 = jwtUser();
            $request['user']       = $users;

            if($users->user_type == 'merchant'){
                $request['merchant']  = $users->merchant->id;
                $payment_accounts     = $users->merchant->paymentAccount;
                $data                 = $this->merchantBalance($users->merchant->id);
                $current_payable      = $data['current_payable'];

                if ($this->withdrawRepo->store($request)) :
                    return $this->responseWithSuccess('created_successfully');
                else :
                    return $this->responseWithSuccess('something_went_wrong_please_try_again');
                endif;

            }elseif($users->user_type == 'merchant_staff'){
                $request['merchant']     = $users->merchant_id;
                $payment_accounts        = $users->staffMerchant->paymentAccount;
                $data                    = $this->staffMerchantBalance($users->merchant_id);
                $current_payable         = $data['current_payable'];
                if (number_format($current_payable, 2, '.', '') != number_format($request->amount, 2, '.', '')):
                    return $this->responseWithSuccess('incorrect_amount_please_try_again');
                else:
                    if($this->withdrawRepo->store($request)):
                        return $this->responseWithSuccess('created_successfully');
                    else:
                        return $this->responseWithSuccess('something_went_wrong_please_try_again');
                    endif;
                endif;
            }

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function payoutBalance()
    {

        try {

            $user                      = jwtUser();
            if ($user->user_type == 'merchant') {
                $request['merchant']   = $user->merchant->id;
            }elseif($user->user_type == 'merchant_staff') {
                $request['merchant']   = $user->merchant_id;
            }

            $data                   = $this->merchantBalance($request['merchant']);
            $current_payable        = $data['current_payable'];

            $data = [
                'current_payable' => $current_payable,
            ];

            return $this->responseWithSuccess('payout_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


}
