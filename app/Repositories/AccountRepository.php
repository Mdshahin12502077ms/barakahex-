<?php

namespace App\Repositories;

use App\Models\Parcel;
use App\Models\DeliveryMan;
use App\Models\StaffAccount;
use App\Models\Account\GovtVat;
use App\Traits\CommonHelperTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Account\DeliveryManAccount;
use App\Repositories\Interfaces\AccountInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;


class AccountRepository implements AccountInterface
{
    use CommonHelperTrait;

    public function all()
    {
        return CompanyAccount::all();
    }


    public function paginate($limit)
    {
        return CompanyAccount::where('type', 'income')
            ->where('create_type', 'user_defined')
            ->when(!hasPermission('read_all_income'), function ($q) {
                $q->where('created_by', Sentinel::getUser()->id);
            })
            ->orderByDesc('id')
            ->paginate($limit);
    }

    public function get($id)
    {
        return CompanyAccount::find($id);
    }

    public function save($role, $data)
    {

    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $income = new CompanyAccount();
            $income->amount = $request->amount;
            $income->date = date('Y-m-d', strtotime($request->date));
            $income->delivery_man_id = $request->delivery_man;
            $income->parcel_id = $request->parcel != '' ? $request->parcel : null;
            $income->created_by = Sentinel::getUser()->id;
            $income->user_id = Sentinel::getUser()->id;
            $income->account_id = $request->account;
            $income->details = $request->details;
            $income->type = 'income';
            $income->create_type = 'user_defined';
            $income->source = 'cash_receive_from_delivery_man';
            $income->save();

            $delivery_account = new DeliveryManAccount();
            $delivery_account->company_account_id = $income->id;
            $delivery_account->delivery_man_id = $request->delivery_man;
            $delivery_account->parcel_id = $request->parcel != '' ? $request->parcel : null;
            $delivery_account->date = date('Y-m-d', strtotime($request->date));
            $delivery_account->source = 'cash_given_to_staff';
            $delivery_account->amount = $request->amount;
            $delivery_account->type = 'expense';
            $delivery_account->details = $request->details;
            $delivery_account->save();

            $staff_account = new StaffAccount();
            $staff_account->date = date('Y-m-d', strtotime($request->date));
            $staff_account->user_id = Sentinel::getUser()->id;
            $staff_account->account_id = $request->account;
            $staff_account->company_account_id = $income->id;
            $staff_account->amount = $request->amount;
            $staff_account->details = 'cash_receive_from_delivery_man';
            $staff_account->type = 'income';
            $staff_account->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $company_account = CompanyAccount::find($request->id);
            $company_account->amount = $request->amount;
            $company_account->date = date('Y-m-d', strtotime($request->date));
            $company_account->delivery_man_id = $request->delivery_man;
            $company_account->parcel_id = $request->parcel != '' ? $request->parcel : null;
            $company_account->created_by = Sentinel::getUser()->id;
            $company_account->user_id = Sentinel::getUser()->id;
            $company_account->account_id = $request->account;
            $company_account->details = $request->details;
            $company_account->create_type = 'user_defined';
            $company_account->save();

            $delivery_account = DeliveryManAccount::where('company_account_id', $company_account->id)->orderByDesc('id')->first();
            $delivery_account->company_account_id = $company_account->id;
            $delivery_account->delivery_man_id = $request->delivery_man;
            $delivery_account->parcel_id = $request->parcel != '' ? $request->parcel : null;
            $delivery_account->date = date('Y-m-d', strtotime($request->date));
            $delivery_account->amount = $request->amount;
            $delivery_account->details = $request->details;
            $delivery_account->save();

            $staff_account = StaffAccount::where('company_account_id', $company_account->id)->orderByDesc('id')->first();

            if ($staff_account) {
                $staff_account->date = date('Y-m-d', strtotime($request->date));
                $staff_account->amount = $request->amount;
                $staff_account->user_id = Sentinel::getUser()->id;
                $staff_account->account_id = $request->account;
                $staff_account->company_account_id = $company_account->id;
                $staff_account->save();
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            CompanyAccount::find($id)->delete();
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function incomeExpenseManage($id, $status, $purpose = '')
    {

        if ($status == 'received'):
            $parcel = Parcel::find($id);

            $delivery_man = DeliveryMan::find($parcel->pickup_man_id);

            if ($purpose == "received-reverse"):
                $parcel->pickup_fee = $delivery_man->pick_up_fee;
                $parcel->save();
            endif;

            $company_account = new CompanyAccount();
            $company_account->parcel_id = $parcel->id;
            $company_account->delivery_man_id = $delivery_man->id;
            $company_account->date = date('Y-m-d');
            $company_account->source = 'pickup_commission';
            $company_account->type = 'expense';
            $company_account->details = 'parcel_pickup_commission_to_pickup_man';
            $company_account->amount = $parcel->pickup_fee;
            $company_account->save();

            // pickup fee entry
            $delivery_account = new DeliveryManAccount();
            $delivery_account->delivery_man_id = $delivery_man->id;
            $delivery_account->parcel_id = $parcel->id;
            $delivery_account->company_account_id = $company_account->id;
            $delivery_account->date = date('Y-m-d');
            $delivery_account->source = 'pickup_commission';
            $delivery_account->amount = $parcel->pickup_fee;
            $delivery_account->type = 'expense'; //previously was income, change by tayeb as debit so expense
            $delivery_account->details = 'parcel_pickup_commission_received';
            $delivery_account->save();

        endif;

        if ($status == 'delivered' || $status == 'partially-delivered'):

            $parcel = Parcel::find($id);
            $delivery_man = DeliveryMan::find($parcel->delivery_man_id);


            // merchant account entry
            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'parcel_delivery';
            $merchant_account->amount = $parcel->price;
            $merchant_account->type = 'income';
            $merchant_account->details = 'parcel_cod_collection_from_customer';
            $merchant_account->save();

            $vat = $parcel->vat ?? 0.00;
            $total_delivery_charge = $parcel->total_delivery_charge;
            $total_vat = floor($total_delivery_charge / 100 * $vat);
            $total_delivery_charge = $total_delivery_charge - $total_vat;

            // if( number_format(number_format($total_delivery_charge, 2, '.', '') + number_format($parcel->payable, 2, '.', ''), 2, '.', '')  != number_format($parcel->price, 2, '.', '')){
            //     $total_delivery_charge = $total_delivery_charge - 0.01;
            // }

            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'delivery_charge';
            $merchant_account->amount = floor($total_delivery_charge);
            $merchant_account->type = 'expense';
            $merchant_account->details = __('parcel_total_delivery_charge');
            $merchant_account->save();

            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'vat_adjustment';
            $merchant_account->amount = floor($total_vat);
            $merchant_account->type = 'expense';
            $merchant_account->details = 'govt_vat_for_parcel_delivery';
            $merchant_account->save();

            // Vat
            $govt_vat = new GovtVat();
            $govt_vat->amount = floor($total_vat);
            $govt_vat->source = 'parcel_delivery';
            $govt_vat->parcel_id = $parcel->id;
            $govt_vat->date = date('Y-m-d');
            $govt_vat->details = 'parcel_successfully_delivered_vat';
            $govt_vat->type = 'income';
            $govt_vat->save();

            // delivery man account entry for cod
            $delivery_account = new DeliveryManAccount();
            $delivery_account->delivery_man_id = $delivery_man->id;
            $delivery_account->parcel_id = $parcel->id;
            $delivery_account->date = date('Y-m-d');
            $delivery_account->source = 'cash_collection';
            $delivery_account->amount = $parcel->price;
            $delivery_account->type = 'income';
            $delivery_account->details = 'parcel_cod_collection_from_customer';
            $delivery_account->save();

            $company_account = new CompanyAccount();
            $company_account->parcel_id = $parcel->id;
            $company_account->delivery_man_id = $delivery_man->id;
            $company_account->date = date('Y-m-d');
            $company_account->source = 'parcel_delivery';
            $company_account->type = 'expense';
            $company_account->details = 'parcel_delivery_commission_to_delivery_man';
            $company_account->amount = $parcel->delivery_fee;
            $company_account->save();

            // Delivery fee entry
            $delivery_account = new DeliveryManAccount();
            $delivery_account->delivery_man_id = $delivery_man->id;
            $delivery_account->parcel_id = $parcel->id;
            $delivery_account->company_account_id = $company_account->id;
            $delivery_account->date = date('Y-m-d');
            $delivery_account->source = 'parcel_delivery';
            $delivery_account->amount = $parcel->delivery_fee;
            $delivery_account->type = 'expense';
            $delivery_account->details = 'parcel_delivery_commission_received';
            $delivery_account->save();

        endif;

        if ($status == 'returned-to-merchant'):
            $parcel = Parcel::find($id);
            $delivery_man = DeliveryMan::find($parcel->return_delivery_man_id);

            $vat = $parcel->vat ?? 0.00;

            $return_charge = 0.00;

            if (settingHelper('return_charge_type') == 'on_demand'):
                if ($parcel->location == 'inside_city'):
                    $return_charge = settingHelper('return_charge_dhaka') != '' ? settingHelper('return_charge_dhaka') : 0.00;
                elseif ($parcel->location == 'sub_city'):
                    $return_charge = settingHelper('return_charge_sub_city') != '' ? settingHelper('return_charge_sub_city') : 0.00;
                elseif ($parcel->location == 'sub_urban_area'):
                    $return_charge = settingHelper('return_charge_outside_dhaka') != '' ? settingHelper('return_charge_outside_dhaka') : 0.00;
                endif;
            else:
                $return_charge = ($parcel->charge + $parcel->cod_charge + $parcel->fragile_charge + $parcel->packaging_charge); //as total_delivery_charge already contains a vat percantage from the begining
            endif;

            $parcel->return_charge = $return_charge;
            $parcel->save();

            $total_vat = ($return_charge / 100) * $vat;

            $company_account = new CompanyAccount();
            $company_account->parcel_id = $parcel->id;
            $company_account->delivery_man_id = $delivery_man->id;
            $company_account->date = date('Y-m-d');
            $company_account->source = 'parcel_return';
            $company_account->type = 'expense';
            $company_account->details = 'parcel_return_commission_to_delivery_man';
            $company_account->amount = $parcel->return_fee;
            $company_account->save();

            // return fee entry
            $delivery_account = new DeliveryManAccount();
            $delivery_account->delivery_man_id = $delivery_man->id;
            $delivery_account->parcel_id = $parcel->id;
            $delivery_account->company_account_id = $company_account->id;
            $delivery_account->date = date('Y-m-d');
            $delivery_account->source = 'parcel_return';
            $delivery_account->amount = $parcel->return_fee;
            $delivery_account->type = 'expense';
            $delivery_account->details = 'parcel_return_commission_received';
            $delivery_account->save();

            // merchant return charge
            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'parcel_return';
            $merchant_account->amount = $return_charge;
            $merchant_account->type = 'expense';
            $merchant_account->details = __('parcel_return_fee');
            $merchant_account->save();

            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'vat_adjustment';
            $merchant_account->amount = floor($total_vat);
            $merchant_account->type = 'expense';
            $merchant_account->details = 'govt_vat_for_parcel_return';
            $merchant_account->save();
            // End Vat Calculate

            $govt_vat = new GovtVat();
            $govt_vat->amount = floor($total_vat);
            $govt_vat->source = 'parcel_return';
            $govt_vat->parcel_id = $parcel->id;
            $govt_vat->date = date('Y-m-d');
            $govt_vat->details = 'parcel_return_delivered_vat';
            $govt_vat->type = 'income';
            $govt_vat->save();


        endif;

        if ($status == 'delivery-reverse'):
            // should be all reverse if income then expense, if expense then income

            $parcel = Parcel::find($id);
            $delivery_man = DeliveryMan::find($parcel->delivery_man_id);


            if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup'):

                $delivery_man = DeliveryMan::find($parcel->pickup_man_id);

                $company_account = new CompanyAccount();
                $company_account->parcel_id = $parcel->id;
                $company_account->delivery_man_id = $delivery_man->id;
                $company_account->date = date('Y-m-d');
                $company_account->source = 'pickup_commission';
                $company_account->type = 'income';
                $company_account->details = 'parcel_pickup_commission_to_pickup_man_reverse_cause_delivery_reverse';
                $company_account->amount = $parcel->pickup_fee;
                $company_account->save();

                // pickup fee entry
                $delivery_account = new DeliveryManAccount();
                $delivery_account->delivery_man_id = $delivery_man->id;
                $delivery_account->parcel_id = $parcel->id;
                $delivery_account->company_account_id = $company_account->id;
                $delivery_account->date = date('Y-m-d');
                $delivery_account->source = 'pickup_commission';
                $delivery_account->amount = $parcel->pickup_fee;
                $delivery_account->type = 'income'; //previously was income, change by tayeb as debit so expense
                $delivery_account->details = 'parcel_pickup_commission_received_reverse_cause_delivery_reverse';
                $delivery_account->save();
            endif;

            // merchant account entry
            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'parcel_delivery';
            $merchant_account->amount = $parcel->price;
            $merchant_account->type = 'expense';
            $merchant_account->details = 'parcel_cod_collection_from_customer_reverse_cause_delivery_reverse';
            $merchant_account->save();

            $vat = $parcel->vat ?? 0.00;
            $total_delivery_charge = $parcel->total_delivery_charge;
            $total_vat = floor($total_delivery_charge / 100 * $vat);
            $total_delivery_charge = $total_delivery_charge - $total_vat;

            // for adjust 0.01 amount
            // if( number_format(number_format($total_delivery_charge, 2, '.', '') + number_format($parcel->payable, 2, '.', ''), 2, '.', '')  != number_format($parcel->price, 2, '.', '')){
            //     $total_delivery_charge = $total_delivery_charge - 0.01;
            // }

            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'delivery_charge';
            $merchant_account->amount = floor($total_delivery_charge);
            $merchant_account->type = 'income';
            $merchant_account->details = __('parcel_total_delivery_charge_revers');
            $merchant_account->save();

            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'vat_adjustment';
            $merchant_account->amount = floor($total_vat);
            $merchant_account->type = 'income';
            $merchant_account->details = 'govt_vat_for_parcel_delivery_reverse_cause_delivery_reverse';
            $merchant_account->save();

            // Vat
            $govt_vat = new GovtVat();
            $govt_vat->amount = floor($total_vat);
            $govt_vat->source = 'parcel_delivery';
            $govt_vat->parcel_id = $parcel->id;
            $govt_vat->date = date('Y-m-d');
            $govt_vat->details = 'parcel_successfully_delivered_vat';
            $govt_vat->type = 'expense';
            $govt_vat->save();


            // delivery man account entry for cod
            $delivery_account = new DeliveryManAccount();
            $delivery_account->delivery_man_id = $delivery_man->id;
            $delivery_account->parcel_id = $parcel->id;
            $delivery_account->date = date('Y-m-d');
            $delivery_account->source = 'cash_collection';
            $delivery_account->amount = $parcel->price;
            $delivery_account->type = 'expense';
            $delivery_account->details = 'parcel_cod_collection_from_customer_reverse_cause_delivery_reverse';
            $delivery_account->save();

            $company_account = new CompanyAccount();
            $company_account->parcel_id = $parcel->id;
            $company_account->delivery_man_id = $delivery_man->id;
            $company_account->date = date('Y-m-d');
            $company_account->source = 'parcel_delivery';
            $company_account->type = 'income';
            $company_account->details = 'parcel_delivery_commission_to_delivery_man_reverse_cause_delivery_reverse';
            $company_account->amount = $parcel->delivery_fee;
            $company_account->save();

            // Delivery fee entry
            $delivery_account = new DeliveryManAccount();
            $delivery_account->delivery_man_id = $delivery_man->id;
            $delivery_account->parcel_id = $parcel->id;
            $delivery_account->company_account_id = $company_account->id;
            $delivery_account->date = date('Y-m-d');
            $delivery_account->source = 'parcel_delivery';
            $delivery_account->amount = $parcel->delivery_fee;
            $delivery_account->type = 'income';
            $delivery_account->details = 'parcel_delivery_commission_received_reverse_cause_delivery_reverse';
            $delivery_account->save();

        endif;

        return true;
    }

    public function incomeExpenseManageReverse($id, $status)
    {

        $parcel = Parcel::find($id);
        $current_status = $parcel->status;

        if (
            ($current_status == "delivered" || $current_status == 'partially-delivered') &&
            ($status == 'received' || $status == 'delivery-assigned' || $status == 'transferred-received-by-hub' || $status == 'transferred-to-hub')
        ) {

            $this->deliveryTransactionReverse($id);

        } elseif (
            ($current_status == "delivered" || $current_status == 'partially-delivered') && ($status == 'pending'
                || $status == 'pickup-assigned' || $status == 'received-by-pickup-man')
        ) {

            $this->deliveryTransactionReverse($id);
            $this->receivedTransactionReverse($id);

        } elseif (
            ($current_status == "received" || $current_status == 'transferred-received-by-hub'
                || $current_status == 'transferred-to-hub' || $current_status == "delivery-assigned" || $current_status == "re-schedule-delivery")
            && ($status == 'pending' || $status == 'pickup-assigned' || $status == 'received-by-pickup-man')
        ) {
            //received all reversed
            //  dd('3');

            //            if($parcel->status_before_cancel != "received-by-pickup-man"){
            //                $this->receivedTransactionReverse($id);
            //            }
            $this->receivedTransactionReverse($id);

        } elseif (
            ($current_status == "returned-to-merchant" && $parcel->is_partially_delivered) &&
            ($status == 'partially-delivered' || $status == 'returned-to-branch' || $status == 'return-assigned-to-merchant')
        ) {
            //returned-to-merchant all reversed
            //dd('4');

            $this->returnTransactionReverse($id);

        } elseif (
            ($current_status == "returned-to-merchant" && $parcel->is_partially_delivered) &&
            ($status == 'received' || $status == 'transferred-received-by-hub' || $status == 'transferred-to-hub'
                || $status == 'delivery-assigned')
        ) {
            //returned-to-merchant all reversed
            //dd('5');

            $this->returnTransactionReverse($id);
            $this->deliveryTransactionReverse($id);

        } elseif (
            ($current_status == "returned-to-merchant" && $parcel->is_partially_delivered) &&
            ($status == 'pending' || $status == 'pickup-assigned' || $status == 'received-by-pickup-man')
        ) {
            //returned-to-merchant all reversed
            //dd('6');

            //            if($parcel->status_before_cancel != "received-by-pickup-man"){
            //                $this->receivedTransactionReverse($id);
            //            }
            $this->returnTransactionReverse($id);
            $this->deliveryTransactionReverse($id);
            $this->receivedTransactionReverse($id);

        } elseif (
            ($current_status == "returned-to-merchant") &&
            ($status == 'received' || $status == 'transferred-received-by-hub' || $status == 'transferred-to-hub'
                || $status == 'delivery-assigned' || $status == 'returned-to-branch' || $status == 'return-assigned-to-merchant')
        ) {
            //returned-to-merchant all reversed
            //dd('7');

            $this->returnTransactionReverse($id);

        } elseif (
            ($current_status == "returned-to-merchant") &&
            ($status == 'pending' || $status == 'pickup-assigned' || $status == 'received-by-pickup-man')
        ) {
            //received, return to merchnt all reversed

            // dd('8');
            //            if($parcel->status_before_cancel != "received-by-pickup-man"){
            //                $this->receivedTransactionReverse($id);
            //            }
            $this->returnTransactionReverse($id);
            $this->receivedTransactionReverse($id);

        } elseif (
            ($current_status == "return-assigned-to-merchant" || $current_status == "returned-to-branch" || $current_status == "partially-delivered")
            && $parcel->is_partially_delivered &&
            ($status == 'received' || $status == 'transferred-received-by-hub' || $status == 'transferred-to-hub'
                || $status == 'delivery-assigned')
        ) {
            //received reversed

            // dd('9');
            $this->deliveryTransactionReverse($id);
        } elseif (
            ($current_status == "return-assigned-to-merchant" || $current_status == "returned-to-branch" || $current_status == "partially-delivered")
            && $parcel->is_partially_delivered &&
            ($status == 'pending' || $status == 'pickup-assigned' || $status == 'received-by-pickup-man')
        ) {
            //received reversed

            // dd('10');
            //            if($parcel->status_before_cancel != "received-by-pickup-man"){
            //                $this->receivedTransactionReverse($id);
            //            }
            $this->deliveryTransactionReverse($id);
            $this->receivedTransactionReverse($id);
        } elseif (
            ($current_status == "return-assigned-to-merchant" || $current_status == "returned-to-branch") &&
            ($status == 'pending' || $status == 'pickup-assigned' || $status == 'received-by-pickup-man')
        ) {
            //received reversed

            // dd('11');
            //            if($parcel->status_before_cancel != "received-by-pickup-man"){
            //                $this->receivedTransactionReverse($id);
            //            }
            $this->receivedTransactionReverse($id);
        }

        return true;

    }


    public function deliveryTransactionReverse($id)
    {

        $parcel = Parcel::find($id);

        $this->withdrawReverseManage($id);

        $delivery_man = DeliveryMan::find($parcel->delivery_man_id);


        $merchant_account = new MerchantAccount();
        $merchant_account->parcel_id = $parcel->id;
        $merchant_account->merchant_id = $parcel->merchant_id;
        $merchant_account->date = date('Y-m-d');
        $merchant_account->source = 'parcel_delivery';
        $merchant_account->amount = $parcel->price;
        $merchant_account->type = 'expense';
        $merchant_account->details = 'parcel_cod_collection_from_customer_reversed';
        $merchant_account->save();

        $vat = $parcel->vat ?? 0.00;
        $total_delivery_charge = $parcel->total_delivery_charge;
        $total_vat = floor($total_delivery_charge / 100 * $vat);
        $total_delivery_charge = $total_delivery_charge - $total_vat;



        $merchant_account = new MerchantAccount();
        $merchant_account->parcel_id = $parcel->id;
        $merchant_account->merchant_id = $parcel->merchant_id;
        $merchant_account->date = date('Y-m-d');
        $merchant_account->source = 'delivery_charge';
        $merchant_account->amount = floor($total_delivery_charge);
        $merchant_account->type = 'income';
        $merchant_account->details = 'parcel_total_delivery_charge_reversed';
        $merchant_account->save();

        $merchant_account = new MerchantAccount();
        $merchant_account->parcel_id = $parcel->id;
        $merchant_account->merchant_id = $parcel->merchant_id;
        $merchant_account->date = date('Y-m-d');
        $merchant_account->source = 'vat_adjustment';
        $merchant_account->amount = floor($total_vat);
        $merchant_account->type = 'income';
        $merchant_account->details = 'govt_vat_for_parcel_delivery_reversed';
        $merchant_account->save();

        // Vat
        $govt_vat = new GovtVat();
        $govt_vat->amount = floor($total_vat);
        $govt_vat->source = 'parcel_delivery';
        $govt_vat->parcel_id = $parcel->id;
        $govt_vat->date = date('Y-m-d');
        $govt_vat->details = 'parcel_successfully_delivered_vat_reversed';
        $govt_vat->type = 'expense'; // previous income
        $govt_vat->save();


        // delivery man account entry for cod
        $delivery_account = new DeliveryManAccount();
        $delivery_account->delivery_man_id = $delivery_man->id;
        $delivery_account->parcel_id = $parcel->id;
        $delivery_account->date = date('Y-m-d');
        $delivery_account->source = 'cash_collection';
        $delivery_account->amount = $parcel->price;
        $delivery_account->type = 'expense'; // previous income
        $delivery_account->details = 'parcel_cod_collection_from_customer_reversed';
        $delivery_account->save();

        $company_account = new CompanyAccount();
        $company_account->parcel_id = $parcel->id;
        $company_account->delivery_man_id = $delivery_man->id;
        $company_account->date = date('Y-m-d');
        $company_account->source = 'parcel_delivery';
        $company_account->type = 'income'; // previous expense
        $company_account->details = 'parcel_delivery_commission_to_delivery_man_reversed';
        $company_account->amount = $parcel->delivery_fee;
        $company_account->save();

        // Delivery fee entry
        $delivery_account = new DeliveryManAccount();
        $delivery_account->delivery_man_id = $delivery_man->id;
        $delivery_account->parcel_id = $parcel->id;
        $delivery_account->company_account_id = $company_account->id;
        $delivery_account->date = date('Y-m-d');
        $delivery_account->source = 'parcel_delivery';
        $delivery_account->amount = $parcel->delivery_fee;
        $delivery_account->type = 'income'; // previous expense
        $delivery_account->details = 'parcel_delivery_commission_received_reversed';
        $delivery_account->save();

        return true;

    }

    public function receivedTransactionReverse($id)
    {

        $parcel = Parcel::find($id);
        $delivery_man = DeliveryMan::find($parcel->pickup_man_id);

        $company_account = new CompanyAccount();
        $company_account->parcel_id = $parcel->id;
        $company_account->delivery_man_id = $delivery_man->id;
        $company_account->date = date('Y-m-d');
        $company_account->source = 'pickup_commission';
        $company_account->type = 'income'; // previous expense
        $company_account->details = 'parcel_pickup_commission_to_pickup_man_reversed';
        $company_account->amount = $parcel->pickup_fee;
        $company_account->save();

        // pickup fee entry
        $delivery_account = new DeliveryManAccount();
        $delivery_account->delivery_man_id = $delivery_man->id;
        $delivery_account->parcel_id = $parcel->id;
        $delivery_account->company_account_id = $company_account->id;
        $delivery_account->date = date('Y-m-d');
        $delivery_account->source = 'pickup_commission';
        $delivery_account->amount = $parcel->pickup_fee;
        $delivery_account->type = 'income'; // // previous expense
        $delivery_account->details = 'parcel_pickup_commission_received_reversed';
        $delivery_account->save();

        return true;

    }

    public function returnTransactionReverse($id)
    {

        $parcel = Parcel::find($id);
        $return_charge = $parcel->return_charge;
        $parcel->return_charge = 0.00;
        $parcel->save();
        $delivery_man = DeliveryMan::find($parcel->return_delivery_man_id);

        $vat = $parcel->vat ?? 0.00;
        $total_vat = $return_charge / 100 * $vat;

        $company_account = new CompanyAccount();
        $company_account->parcel_id = $parcel->id;
        $company_account->delivery_man_id = $delivery_man->id;
        $company_account->date = date('Y-m-d');
        $company_account->source = 'parcel_return';
        $company_account->type = 'income'; // previous expense
        $company_account->details = 'parcel_return_commission_to_delivery_man_reversed';
        $company_account->amount = $parcel->return_fee;
        $company_account->save();

        // return fee entry
        $delivery_account = new DeliveryManAccount();
        $delivery_account->delivery_man_id = $delivery_man->id;
        $delivery_account->parcel_id = $parcel->id;
        $delivery_account->company_account_id = $company_account->id;
        $delivery_account->date = date('Y-m-d');
        $delivery_account->source = 'parcel_return';
        $delivery_account->amount = $parcel->return_fee;
        $delivery_account->type = 'income'; // previous expense
        $delivery_account->details = 'parcel_return_commission_received_reversed';
        $delivery_account->save();

        // merchant return charge
        $merchant_account = new MerchantAccount();
        $merchant_account->parcel_id = $parcel->id;
        $merchant_account->merchant_id = $parcel->merchant_id;
        $merchant_account->date = date('Y-m-d');
        $merchant_account->source = 'parcel_return';
        $merchant_account->amount = $return_charge;
        $merchant_account->type = 'income'; // previous expense
        $merchant_account->details = 'parcel_return_fee_as_parcel_returned_reversed';
        $merchant_account->save();

        $merchant_account = new MerchantAccount();
        $merchant_account->parcel_id = $parcel->id;
        $merchant_account->merchant_id = $parcel->merchant_id;
        $merchant_account->date = date('Y-m-d');
        $merchant_account->source = 'vat_adjustment';
        $merchant_account->amount = floor($total_vat);
        $merchant_account->type = 'income'; // previous expense
        $merchant_account->details = 'govt_vat_for_parcel_return_reversed';
        $merchant_account->save();

        // End Vat Calculate
        $govt_vat = new GovtVat();
        $govt_vat->amount = floor($total_vat);
        $govt_vat->source = 'parcel_return';
        $govt_vat->parcel_id = $parcel->id;
        $govt_vat->date = date('Y-m-d');
        $govt_vat->details = 'parcel_return_delivered_vat_reversed';
        $govt_vat->type = 'expense'; // previous income
        $govt_vat->save();

        return true;

    }

    public function incomeExpenseManageCancel($id, $status)
    {
        $parcel = Parcel::find($id);

        if ($status == 'cancel') {

            if (
                $parcel->status == 'received' || $parcel->status == 'transferred-to-branch'
                || $parcel->status == 'transferred-received-by-branch' || $parcel->status == 'delivery-assigned'
                || $parcel->status == 're-schedule-delivery' || $parcel->status == 'returned-to-warehouse'
                || $parcel->status == 'return-assigned-to-merchant'
            ) {
                $this->receivedTransactionReverse($id);
            }

        } elseif ($status == 'reverse-cancel') {

            if (
                $parcel->status_before_cancel == 'received' || $parcel->status_before_cancel == 'transferred-to-branch'
                || $parcel->status_before_cancel == 'transferred-received-by-branch' || $parcel->status_before_cancel == 'delivery-assigned'
                || $parcel->status_before_cancel == 're-schedule-delivery' || $parcel->status_before_cancel == 'returned-to-warehouse'
                || $parcel->status_before_cancel == 'return-assigned-to-merchant'
            ) {
                $this->incomeExpenseManage($id, 'received', 'received-reverse');
            }

        }

        return true;
    }


    public function creditStore($request)
    {
        DB::beginTransaction();
        try {
            //company table inserting the credit amount
            $company_account = new CompanyAccount();
            $company_account->source = 'delivery_charge_receive_from_merchant';
            $company_account->details = $request->details;
            $company_account->date = date('Y-m-d', strtotime($request->date));
            $company_account->merchant_id = $request->merchant;
            $company_account->type = 'income';
            $company_account->amount = $request->amount;
            $company_account->created_by = Sentinel::getUser()->id;
            $company_account->user_id = Sentinel::getUser()->id;
            $company_account->create_type = 'user_defined';
            $company_account->account_id = $request->account;
            if (!blank($request->parcel)):
                $company_account->parcel_id = $request->parcel;
            endif;
            $company_account->save();
            //end for company

            //staff account calculation and insertion
            $staff_account = new StaffAccount();
            $staff_account->source = 'delivery_charge';
            $staff_account->details = 'delivery_charge_receive_from_merchant';
            $staff_account->date = date('Y-m-d', strtotime($request->date));
            $staff_account->type = 'income';
            $staff_account->amount = $request->amount;
            $staff_account->user_id = $company_account->user_id;
            $staff_account->account_id = $request->account;
            $staff_account->company_account_id = $company_account->id;
            $staff_account->save();
            //end for staff account

            $merchant_account = new MerchantAccount();
            $merchant_account->source = 'cash_given_for_delivery_charge';
            $merchant_account->details = $request->details;
            $merchant_account->date = date('Y-m-d', strtotime($request->date));
            $merchant_account->type = 'income';
            $merchant_account->amount = $request->amount;
            $merchant_account->merchant_id = $request->merchant;
            $merchant_account->company_account_id = $company_account->id;
            if (!blank($request->parcel)):
                $merchant_account->parcel_id = $request->parcel;
            endif;
            $merchant_account->save();

            DB::commit();
            return true;


        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function creditUpdate($request)
    {
        DB::beginTransaction();
        try {
            // get previous company_account record
            $company_account = CompanyAccount::find($request->id);
            $company_account->details = $request->details;
            $company_account->date = date('Y-m-d', strtotime($request->date));
            $company_account->merchant_id = $request->merchant;
            $company_account->amount = $request->amount;
            $company_account->created_by = Sentinel::getUser()->id;
            $company_account->user_id = Sentinel::getUser()->id;
            $company_account->account_id = $request->account;
            if (!blank($request->parcel)):
                $company_account->parcel_id = $request->parcel;
            endif;
            $company_account->save();
            //end for company

            // get previous staff_account record
            $staff_account = StaffAccount::where('company_account_id', $company_account->id)->orderByDesc('id')->first();
            $staff_account->date = date('Y-m-d', strtotime($request->date));
            $staff_account->amount = $request->amount;
            $staff_account->user_id = $company_account->user_id;
            $staff_account->account_id = $request->account;
            $staff_account->company_account_id = $company_account->id;
            $staff_account->save();
            //end for staff account

            //get previous merchant_account record
            $merchant_account = MerchantAccount::where('company_account_id', $company_account->id)->orderByDesc('id')->first();
            $merchant_account->details = $request->details;
            $merchant_account->date = date('Y-m-d', strtotime($request->date));
            $merchant_account->amount = $request->amount;
            $merchant_account->merchant_id = $request->merchant;
            $merchant_account->company_account_id = $company_account->id;
            if (!blank($request->parcel)):
                $merchant_account->parcel_id = $request->parcel;
            endif;
            $merchant_account->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function withdrawReverseManage($id)
    {
        $parcel = Parcel::find($id);

        $previous_withdraw_id = $parcel->withdraw_id;
        //if parcel payable amount already paid to merchant
        if ($parcel->is_paid):
            $parcel->is_paid = false;

            // merchant account entry
            $merchant_account = new MerchantAccount();
            $merchant_account->parcel_id = $parcel->id;
            $merchant_account->merchant_id = $parcel->merchant_id;
            $merchant_account->parcel_withdraw_id = $previous_withdraw_id;
            $merchant_account->date = date('Y-m-d');
            $merchant_account->source = 'paid_parcels_delivery_reverse';
            $merchant_account->amount = $parcel->payable;
            $merchant_account->type = 'expense';
            $merchant_account->details = 'parcel_payable_amount_deducted_cause_delivery_reverse';
            $merchant_account->save();

        else:
            $merchant_withdraw = MerchantWithdraw::find($previous_withdraw_id);

            if (!blank($merchant_withdraw)):
                $merchant_withdraw->amount -= $parcel->payable;
                $merchant_withdraw->save();

                $merchant_account_record = MerchantAccount::where('merchant_withdraw_id', $previous_withdraw_id)->orderByDesc('id')->first();
                $merchant_account_record->amount -= $parcel->payable;
                $merchant_account_record->save();

                $company_account = CompanyAccount::where('merchant_withdraw_id', $previous_withdraw_id)->orderByDesc('id')->first();
                ;
                $company_account->amount -= $parcel->payable;
                $company_account->save();

                if ($merchant_withdraw->amount <= 0):
                    $merchant_withdraw->status = 'rejected';
                    $merchant_withdraw->withdraw_batch_id = null;
                    $merchant_withdraw->save();

                    foreach ($merchant_withdraw->parcels as $parcel):
                        $parcel->withdraw_id = null;
                        $parcel->save();
                    endforeach;
                    foreach ($merchant_withdraw->merchantAccounts as $merchant_accounts):
                        $merchant_accounts->payment_withdraw_id = null;
                        $merchant_accounts->save();
                    endforeach;

                    $company_account = new CompanyAccount();
                    $company_account->source = 'withdraw_rejected';
                    $company_account->details = 'merchant_payment_withdraw_rejected';
                    $company_account->date = date('Y-m-d');
                    $company_account->type = 'income';
                    $company_account->amount = $merchant_withdraw->amount;
                    $company_account->created_by = Sentinel::getUser()->id;
                    $company_account->merchant_id = $merchant_withdraw->merchant_id;
                    $company_account->merchant_withdraw_id = $merchant_withdraw->id;
                    $company_account->reject_reason = __('not_enough_available_balance');
                    $company_account->save();

                    $merchant_account = new MerchantAccount();
                    $merchant_account->source = 'withdraw_rejected';
                    $merchant_account->merchant_withdraw_id = $merchant_withdraw->id;
                    $merchant_account->details = 'withdraw_request_rejected';
                    $merchant_account->date = date('Y-m-d');
                    $merchant_account->type = 'income';
                    $merchant_account->amount = $merchant_withdraw->amount;
                    $merchant_account->merchant_id = $merchant_withdraw->merchant_id;
                    $merchant_account->company_account_id = $company_account->id;
                    $merchant_account->save();
                endif;
            endif;

        endif;

        $parcel->withdraw_id = null;
        $parcel->save();
        //end payment

        return true;
    }
}
