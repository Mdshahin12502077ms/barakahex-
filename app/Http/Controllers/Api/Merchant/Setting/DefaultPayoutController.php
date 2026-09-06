<?php

namespace App\Http\Controllers\Api\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Api\DefaultPayoutResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\MerchantPaymentAccount;
use App\Traits\SendMailTrait;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Interfaces\WithdrawInterface;
class DefaultPayoutController extends Controller
{
    use ApiReturnFormatTrait;

    protected $withdrawRepo;


    public function __construct(WithdrawInterface $withdrawRepo)
    {

        $this->withdrawRepo     = $withdrawRepo;

    }
    public function defaultPayout(Request $request)
    {
        try {
            $user = jwtUser();

            if ($user->user_type == 'merchant') {
                $merchant = Merchant::where('user_id', $user->id)->first();
            } elseif ($user->user_type == 'merchant_staff') {
                $merchant = Merchant::where('id', $user->merchant_id)->first();
            } else {
                return $this->responseWithError('Invalid user type');
            }

            $accounts = MerchantPaymentAccount::with('paymentAccount')
                        ->where('merchant_id', $merchant->id)
                        ->latest()
                        ->get();
            $data = [
                'accounts' => DefaultPayoutResource::collection($accounts),
                'withdraw' => $merchant->withdraw,
            ];

            return $this->responseWithSuccess('Payout retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }


    public function updateDefaultPayout(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        $user                      = jwtUser();
        if ($user->user_type == 'merchant') {
            $request['merchant']   = $user->merchant;
        }elseif($user->user_type == 'merchant_staff') {
            $request['merchant']   = $user->merchant_id;
        }

        try {
            $this->withdrawRepo->updatePaymentMethod($request);
            return $this->responseWithSuccess('default payment updated successfully');

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
