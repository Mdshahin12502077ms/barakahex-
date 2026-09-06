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
use App\Http\Resources\Api\MFSResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\MerchantPaymentAccount;
use App\Traits\SendMailTrait;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Interfaces\WithdrawInterface;
class MFSController extends Controller
{
    use ApiReturnFormatTrait;
    protected $withdrawRepo;

    public function __construct(WithdrawInterface $withdrawRepo)
    {
        $this->withdrawRepo     = $withdrawRepo;
    }

    public function mfs(Request $request)
    {
        try {
            $user   		= jwtUser();

            if (!$user) {
                return $this->responseWithError('User not authenticated');
            }

            if ($user->user_type === 'merchant') {
                $merchant = Merchant::where('user_id', $user->id)->first();
            } elseif ($user->user_type === 'merchant_staff') {
                $merchant = Merchant::where('id', $user->merchant_id)->first();
            } else {
                return $this->responseWithError('Invalid user type');
            }

            if (!$merchant) {
                return $this->responseWithError('Merchant not found');
            }

            $methods       = PaymentMethod::with('payment')->where('type', 'mfs')->get();
            $payments      = MerchantPaymentAccount::where('merchant_id', $merchant->id)->where('type', 'mfs')->get();

            $method        = [];
            if ($payments->isNotEmpty()) {
                $methodIds = $payments->pluck('payment_method_id')->toArray();
                $method    = PaymentMethod::whereIn('id', $methodIds)->with('payment')->get();
            }

            $data = [
                'mfs_method' => MFSResource::collection($method),
            ];

            return $this->responseWithSuccess('MFS retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }



    public function updateMfs(Request $request)
    {
        $user                       = jwtUser();
        try {

            $data                   = $this->updateOthersAccount($request);

            return $this->responseWithSuccess('MFS updated successfully');

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function storeMfs(Request $request)
    {
        $user                       = jwtUser();
        try {

            $data                   = $this->updateOthersAccount($request);

            return $this->responseWithSuccess('MFS Store successfully');

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function updateOthersAccount($data)
    {
        try {

            $user          = jwtUser();

            if ($user) {
                if ($user->user_type == 'merchant') {
                    $merchant               = Merchant::where('user_id', $user->id)->first();
                } elseif ($user->user_type == 'merchant_staff') {
                    $merchant               = Merchant::where('id', $user->merchant_id)->first();
                }
            }


          $payment_account = MerchantPaymentAccount::where('merchant_id', $merchant->id)
            ->where('payment_method_id', $data->payment_method_id)
            ->first();

          $type = PaymentMethod::where('id', $data->payment_method_id)->first();

          if (!$payment_account) {
            $payment_account                    = new MerchantPaymentAccount;
            $payment_account->merchant_id       = $merchant->id;
            $payment_account->payment_method_id = $data->payment_method_id;
          }

          $payment_account->mfs_number        = $data->mfs_number;
          $payment_account->mfs_ac_type       = $data->mfs_ac_type;
          $payment_account->type              = $type->type;
          $payment_account->save();


          return true;


        } catch (\Exception $e) {

            return false;
        }
    }

    public function allMfs(Request $request)
    {
        try {
            $user                       = jwtUser();

            if (!$user) {
                return $this->responseWithError('User not authenticated');
            }



            $methods                    = PaymentMethod::where('type', 'mfs')->get();

            $data = [
                'all_mfs'               => MFSResource::collection($methods),

            ];


            return $this->responseWithSuccess('MFS retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
