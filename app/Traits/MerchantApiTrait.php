<?php

namespace App\Traits;

use App\Models\Merchant;

trait MerchantApiTrait
{
    public function getMerchant($request)
    {
        $merchant = Merchant::where('api_key', $request->header('Api-key'))->where('secret_key', $request->header('Secret-key'))->first();
        $code = 200;

        if ($merchant == null) :
            $response = [
                'status'  => 401,
                'message' => __('unauthorized_access'),
            ];
            $code = 401;
            $data['response'] = $response;
            $data['code']     = $code;

            return $data;
        endif;

        if ($merchant->user->status == \App\Enums\StatusEnum::INACTIVE):
            $response = [
                'status'  => 401,
                'message' => __('your_account_is_inactive'),
            ];
            $code = 401;
        elseif ($merchant->user->status == 2) :
            $response = [
                'status'  => 401,
                'message' => __('your_account_is_suspend'),
            ];
            $code = 401;
        endif;

        if ($code == 401):
            $data['response'] = $response;
            $data['code']     = $code;

            return $data;
        endif;

        $data['code']     = $code;
        $data['merchant'] = $merchant;

        return $data;
    }
}
