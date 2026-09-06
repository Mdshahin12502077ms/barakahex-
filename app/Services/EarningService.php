<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Account\DeliveryManAccount;
use App\Models\Account\Account;
use App\Models\Parcel;
use App\Models\Account\GovtVat;
use Illuminate\Support\Facades\DB;

class EarningService
{
    private $months = [
        'January'   => 'Jan',
        'February'  => 'Feb',
        'March'     => 'Mar',
        'April'     => 'Apr',
        'May'       => 'May',
        'June'      => 'Jun',
        'July'      => 'Jul',
        'August'    => 'Aug',
        'September' => 'Sep',
        'October'   => 'Oct',
        'November'  => 'Nov',
        'December'  => 'Dec',
    ];

    public function totalEarning()
    {
        $data = [];

        $now = date('Y-m-d');



        $merchant = Merchant::query();

        $query = $merchant->select(
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('COUNT(*) as data')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        foreach ($this->months as $full_month => $sort_month) {
            $enrol = $query->firstWhere('month_name', $full_month);
            $data[] = $enrol ? $enrol->data : 0;
        }

        return $data;
    }


    public function profits($start, $end)
    {

        $total_vat_income                         = GovtVat::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('type', 'income')
            ->where('parcel_id', '!=', '')->whereIn('source', ['parcel_delivery', 'parcel_return'])
            ->sum('amount');

        $total_vat_expense                        = GovtVat::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('type', 'expense')
            ->where('parcel_id', '!=', '')->whereIn('source', ['parcel_delivery', 'parcel_return'])
            ->sum('amount');
        // $return_income                            = MerchantAccount::where('date', '>=', $start)
        //     ->where('date', '<=', $end)
        //     ->where('type', 'income')
        //     ->where(function ($query) {
        //         $query->where('source', 'parcel_return')
        //             ->orWhere(function ($query) {
        //                 $query->where('source', 'vat_adjustment')
        //                     ->whereIn('details', ['govt_vat_for_parcel_return', 'govt_vat_for_parcel_return_reversed']);
        //             });
        //     })
        //     ->sum('amount');
        // $return_expense                            = MerchantAccount::where('date', '>=', $start)
        //     ->where('date', '<=', $end)
        //     ->where('type', 'expense')
        //     ->where(function ($query) {
        //         $query->where('source', 'parcel_return')
        //             ->orWhere(function ($query) {
        //                 $query->where('source', 'vat_adjustment')
        //                     ->whereIn('details', ['govt_vat_for_parcel_return', 'govt_vat_for_parcel_return_reversed']);
        //             });
        //     })
        //     ->sum('amount');

        // $data['total_parcel_return_charge']       = $return_expense - $return_income;


        $data['total_vat']                        = $total_vat_income - $total_vat_expense;


        $total_charge_vat                         =  Parcel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where(function ($query) {
                $query->where('is_partially_delivered', true)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->sum('total_delivery_charge');


        $data['total_charge_vat']                 =  $total_charge_vat + $data['total_parcel_return_charge'];


        $total_delivery_charge_income             = DeliveryManAccount::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
            ->where('type', 'income')
            ->sum('amount');

        $total_delivery_charge_expense            = DeliveryManAccount::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
            ->where('type', 'expense')
            ->sum('amount');


        $data['total_delivery_charge']            = $total_delivery_charge_expense - $total_delivery_charge_income;

        $data['total_fragile_charge']             = Parcel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where(function ($query) {
                $query->where('is_partially_delivered', true)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->sum('fragile_charge');
        $data['total_packaging_charge']           = Parcel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where(function ($query) {
                $query->where('is_partially_delivered', true)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->sum('packaging_charge');


        $data['total_profit']                      = abs($data['total_charge_vat']) - $data['total_delivery_charge'] -  $data['total_vat'] + $data['total_fragile_charge'] + $data['total_packaging_charge'];

        $data['total_payable_to_merchant']         = Parcel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where(function ($query) {
                $query->where('is_partially_delivered', true)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->sum('price');

        $data['total_paid_to_merchant']           = MerchantWithdraw::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->whereIn('status', ['processed', 'pending', 'approved'])
            ->sum('amount');

        $data['pending_payments']                 = MerchantWithdraw::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');

        $data['total_paid_by_merchant']           = CompanyAccount::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('source', 'delivery_charge_receive_from_merchant')
            ->where('type', 'income')
            ->where('merchant_id', '!=', '')
            ->sum('amount');

        $data['current_payable']                  = abs($data['total_payable_to_merchant']) + $data['total_paid_by_merchant'] - $data['total_paid_to_merchant'] -  $data['total_charge_vat'];

        $data['total_cash_on_delivery']           = Parcel::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where(function ($query) {
                $query->where('is_partially_delivered', true)
                    ->orWhereIn('status', ['delivered', 'delivered-and-verified']);
            })
            ->sum('price');

        $data['total_paid_by_delivery_man']       = DeliveryManAccount::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('delivery_man_id', '!=', '')
            ->where('source', 'cash_given_to_staff')
            ->where('type', 'expense')
            ->sum('amount');

        $data['total_expense_from_account']        = CompanyAccount::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('type', 'expense')
            ->where('create_type', 'user_defined')
            ->sum('amount');

        $start = $start . ' ' . '00:00:00';
        $end =  $end . ' ' . '23:59:59';

        $data['total_bank_opening_balance']        = Account::where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->sum('balance');

        return $data;
    }
}
