<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Models\Account\MerchantAccount;
use App\Http\Resources\Api\PayoutLogResource;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class PayoutLogController extends Controller
{
    use ApiReturnFormatTrait;

    public function allPayoutLog(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = jwtUser();
            $query = MerchantAccount::query();

            $userPermissions = $user->permissions;

            if ($user->user_type == 'merchant_staff') {
                $query->whereNotIn('source', ['previous_balance', 'cash_given_for_delivery_charge', 'opening_balance'])
                    ->where('merchant_id', $user->merchant_id);

                if (is_array($userPermissions) && !in_array('all_parcel_logs', $userPermissions) && !in_array('all_payment_logs', $userPermissions)) {
                    $query->where(function ($query) use ($user) {
                        $query->whereHas('parcel', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })->orWhereHas('withdraw', function ($q) use ($user) {
                            $q->where('created_by', $user->id);
                        });
                    });
                } elseif (is_array($userPermissions) && !in_array('all_parcel_logs', $userPermissions)) {
                    $query->whereHas('parcel', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->orWhereHas('withdraw');
                } elseif (is_array($userPermissions) && !in_array('all_payment_logs', $userPermissions)) {
                    $query->whereHas('withdraw', function ($q) use ($user) {
                        $q->where('created_by', $user->id);
                    })->orWhereHas('parcel');
                }
            } elseif ($user->user_type == 'merchant') {
                $merchant_id = $user->merchant->id;
                $query->where('merchant_id', $merchant_id);
            }else{
                return $this->responseWithError('Invalid user type');
            }

            $payout_log = $query->latest()->paginate();

            $data = [
                'payout_log' => PayoutLogResource::collection($payout_log),
                'paginate' => [
                    'total'         => $payout_log->total(),
                    'current_page'  => $payout_log->currentPage(),
                    'per_page'      => $payout_log->perPage(),
                    'last_page'     => $payout_log->lastPage(),
                    'prev_page_url' => $payout_log->previousPageUrl(),
                    'next_page_url' => $payout_log->nextPageUrl(),
                    'path'          => $payout_log->path(),
                ],
            ];

            return $this->responseWithSuccess('payout_log_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }
}
