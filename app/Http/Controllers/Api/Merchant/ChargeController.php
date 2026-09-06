<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Models\Account\MerchantAccount;
use App\Http\Resources\Api\ChargeResource;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class ChargeController extends Controller
{
    use ApiReturnFormatTrait;

    public function charge(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = jwtUser();

            $cod_charges = $user->user_type == 'merchant' ? $user->merchant->cod_charges : $user->staffMerchant->cod_charges;
            $charges = $user->user_type == 'merchant' ? $user->merchant->charges : $user->staffMerchant->charges;

            foreach ($charges as $weight => $charge) {
                $result[] = [
                    'weight' => $weight,
                    'same_day' => data_get($charge, 'same_day', 0.0),
                    // 'next_day' => data_get($charge, 'next_day', 0.0),
                    'sub_city' => data_get($charge, 'sub_city', 0.0),
                    'sub_urban_area' => data_get($charge, 'sub_urban_area', 0.0),
                ];
            }
            $data = [
                'charges' => $result,
                'cod_charges' => $cod_charges,

            ];

            return $this->responseWithSuccess('charge_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function packagingCharge(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = jwtUser();

            $data = [
                'packaging_charge' => settingHelper('package_and_charges'),
            ];

            return $this->responseWithSuccess('packaging_charge_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

}
