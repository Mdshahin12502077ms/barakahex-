<?php

namespace App\Repositories\Admin;

use App\Models\Parcel;
use App\Models\PercelOtpLog;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\RepoResponseTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class CrmHistoryRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Parcel $model)
    {
        $this->model = $model;
    }

    public function search($phone_number)
    {
        $phone_number = trim($phone_number);
        $user = Sentinel::getUser();

        $query = $this->model->with('merchant')
            ->where("customer_phone_number", "like", $phone_number . "%");

        if ($user && ($user->user_type == "merchant" || $user->user_type == "merchant_staff")) {
            $merchantId = null;
            if ($user->user_type == "merchant") {
                $merchantId = $user->merchant ? $user->merchant->id : \App\Models\Merchant::where('user_id', $user->id)->value('id');
            } elseif ($user->user_type == "merchant_staff") {
                $merchantId = $user->merchant_id;
            }

            if ($merchantId) {
                $query->where("merchant_id", $merchantId);
            }
        }

        $parcels = $query->latest()->get();
        $total_order = $parcels->count();

        // If no records found, return an empty collection so controller's count() check evaluates correctly
        if ($total_order == 0) {
            return collect([]);
        }

        // 1. Delivered stats (delivered, partially-delivered)
        $delivered_count = $parcels->whereIn('status', ['delivered', 'partially-delivered'])->count();
        $delivered_percent = round(($delivered_count / $total_order) * 100);

        // 2. Returned stats (returned-to-merchant, returned)
        $returned_count = $parcels->filter(function ($p) {
            return in_array($p->status, ['returned-to-merchant', 'returned']) || str_contains($p->status, 'return');
        })->count();
        $returned_percent = round(($returned_count / $total_order) * 100);

        // 3. Cancelled stats (cancel, cancle)
        $cancelled_count = $parcels->whereIn('status', ['cancel', 'cancle'])->count();
        $cancelled_percent = round(($cancelled_count / $total_order) * 100);

        // 4. Total COD Amount (price column in parcels table)
        $total_cod_amount = $parcels->sum(function ($p) {
            return (float) ($p->price ?? 0);
        });

        // 5. Returned COD Amount
        $total_return_amount = $parcels->filter(function ($p) {
            return in_array($p->status, ['returned-to-merchant', 'returned']) || str_contains($p->status, 'return');
        })->sum(function ($p) {
            return (float) ($p->price ?? 0);
        });

        // 6. Customer Profile info (from latest order)
        $latest = $parcels->first();
        $customer_name = $latest->customer_name ?? 'N/A';
        $customer_phone = $latest->customer_phone_number ?? $phone_number;
        $customer_address = $latest->customer_address ?? 'N/A';

        // 7. Trust Score calculation (100 - return rate)
        $trust_score = max(0, min(100, 100 - $returned_percent));
        $risk_label = 'Low Return Risk';
        if ($returned_percent >= 40) {
            $risk_label = 'High Return Risk';
        } elseif ($returned_percent >= 20) {
            $risk_label = 'Moderate Return Risk';
        }

        // 8. Formatted Order History for table
        $history = $parcels->map(function ($p) {
            $merchantName = $p->merchant->company ?? ($p->merchant->user->first_name ?? 'N/A');
            return [
                'id' => $p->id,
                'parcel_no' => $p->parcel_no,
                'merchant_name' => $merchantName,
                'order_date' => $p->created_at ? $p->created_at->format('d-m-Y H:i:s') : 'N/A',
                'cod_amount' => number_format((float) $p->price, 2) . ' BDT',
                'raw_cod' => (float) $p->price,
                'status' => $p->status,
            ];
        });

        return collect([
            'customer' => [
                'name' => $customer_name,
                'phone' => $customer_phone,
                'address' => $customer_address,
                'trust_score' => $trust_score,
                'risk_label' => $risk_label,
            ],
            'stats' => [
                'total_orders' => $total_order,
                'delivered_count' => $delivered_count,
                'delivered_percent' => $delivered_percent,
                'returned_count' => $returned_count,
                'returned_percent' => $returned_percent,
                'cancelled_count' => $cancelled_count,
                'cancelled_percent' => $cancelled_percent,
                'total_cod_amount' => number_format($total_cod_amount, 2) . ' BDT',
                'raw_total_cod' => $total_cod_amount,
                'total_return_amount' => number_format($total_return_amount, 2) . ' BDT',
            ],
            'parcels' => $history,
        ]);
    }

}
