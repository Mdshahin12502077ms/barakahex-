<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Models\Account\MerchantAccount;
use App\Http\Resources\Api\PayoutLogResource;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Merchant;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiReturnFormatTrait;

    public function api(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = jwtUser();

            if (!$user) {
                return $this->responseWithError('User not authenticated');
            }

            $apiSetting = settingHelper('preferences')->where('title', 'read_merchant_api')->first();

            if (!$apiSetting || !$apiSetting->merchant) {
                return abort(403, 'Access Denied');
            }else{
                if ($user->user_type == 'merchant_staff') {
                    $api_key        = $user->staffMerchant->api_key;
                    $secret_key     = $user->staffMerchant->secret_key;
                } elseif ($user->user_type == 'merchant') {
                    $api_key        = $user->merchant->api_key;
                    $secret_key     = $user->merchant->secret_key;
                }

            }

            $data = [
                'api_key'       => $api_key,
                'secret_key'    => $secret_key,
            ];

            return $this->responseWithSuccess('api_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }
}

