<?php

namespace App\Traits;
use App\Models\Account\MerchantAccount;
use App\Models\Parcel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

trait MerchantBalanceTrait {


    public function merchantBalance($merchant_id) {
        $parcels = Parcel::where('merchant_id', $merchant_id)
                    ->where(function ($query) {
                        $query->where('is_partially_delivered', '=', 1)
                            ->orWhereIn('status',['delivered','delivered-and-verified']);
                    })
                    ->where("withdraw_id", "=", null)
                    ->where('is_paid',false)
                    ->get();

        $payable   = $parcels->sum('payable');

        $merchant_accounts = MerchantAccount::where('merchant_id', $merchant_id)
                        ->where(function ($query){
                            $query->whereIn('source', ['previous_balance','cash_given_for_delivery_charge','parcel_return','paid_parcels_delivery_reverse','opening_balance'])
                                ->orWhere(function ($query){
                                    $query->where('source','vat_adjustment')
                                        ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                });
                        })
                        ->where('payment_withdraw_id', null)->where('is_paid',false)->get();


        $income = $merchant_accounts->where('type', 'income')->sum('amount');
        $expense = $merchant_accounts->where('type', 'expense')->sum('amount');

        $current_payable = $payable + $income - $expense;

        $data['current_payable']    = $current_payable;
        $data['parcels']            = $parcels;
        $data['merchant_accounts']  = $merchant_accounts;

        return $data;
    }
    public function staffMerchantBalance($merchant_id) {
        $parcels = Parcel::where('merchant_id', $merchant_id)
            ->where(function ($query) {
                $query->where('is_partially_delivered', '=', 1)
                    ->orWhereIn('status',['delivered','delivered-and-verified']);
            })
            ->when(!hasPermission('all_parcel_payment'), function ($q){
                $q->where('user_id', Sentinel::getUser()->id);
            })
            ->where("withdraw_id", "=", null)
            ->where('is_paid',false)
            ->get();

        $payable   = $parcels->sum('payable');

        $merchant_accounts = MerchantAccount::where('merchant_id', $merchant_id)
            ->where(function ($query){
                $query->whereHas('parcel',function ($query){
                    $query->when(!hasPermission('all_parcel_payment'), function ($inner){
                            $inner->where('user_id', Sentinel::getUser()->id);
                        });
                    })
                    ->whereIn('source', ['parcel_return','paid_parcels_delivery_reverse'])
                    ->orWhere(function ($q){
                        $q->where('source','vat_adjustment')
                            ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                    });
            })
            ->where('payment_withdraw_id', null)->where('is_paid',false)->get();

        $income = $merchant_accounts->where('type', 'income')->sum('amount');
        $expense = $merchant_accounts->where('type', 'expense')->sum('amount');

        $current_payable = $payable + $income - $expense;

        $data['current_payable']    = $current_payable;
        $data['parcels']            = $parcels;
        $data['merchant_accounts']  = $merchant_accounts;

        return $data;
    }

    public function withdrawUpdateMerchantBalance($withdraw_id)
    {
        $withdraw           = $this->withdraws->get($withdraw_id);
        $parcels            = $withdraw->parcels;
        $merchant_accounts  = $withdraw->merchantAccounts;


        $income  = $merchant_accounts->where('type', 'income')->sum('amount');
        $expense = $merchant_accounts->where('type', 'expense')->sum('amount');

        $payable = $parcels->sum('payable');

        $current_payable = $payable + $income - $expense;

        return $current_payable;
    }

}
