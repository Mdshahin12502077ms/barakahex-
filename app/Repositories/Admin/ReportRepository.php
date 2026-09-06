<?php

namespace App\Repositories\Admin;

use App\Models\Account\Account;
use App\Repositories\Interfaces\Admin\ReportInterface;
use App\Models\Account\MerchantWithdraw;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\DeliveryManAccount;
use App\Models\Account\GovtVat;
use App\Models\Parcel;
use DB;

class ReportRepository implements ReportInterface {
    public function parcelSearch($request)
    {
        try{
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date   = date('Y-m-d', strtotime($request->end_date));

            $merchant = $request->merchant;
            if ($request->status != ''):
                if ($request->status == 'pending'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'deleted'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'received-by-pickup-man'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'pickup-assigned'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 're-schedule-pickup'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'received'):
                    $data[__('received_by_warehouse')] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'delivery-assigned'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 're-schedule-delivery'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'returned-to-warehouse'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'return-assigned-to-merchant'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'returned-to-merchant'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'delivered'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'cancelled'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 're-request'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                elseif ($request->status == 'partially-delivered'):
                    $data[__($request->status)] = $this->eventQuery($start_date, $end_date, $merchant, $request->status);
                endif;
            else:
                $data[__('pending')]                      = $this->eventQuery($start_date, $end_date, $merchant, 'pending');
                $data[__('pickup-assigned')]              = $this->eventQuery($start_date, $end_date, $merchant, 'pickup-assigned');
                $data[__('re-schedule-pickup')]           = $this->eventQuery($start_date, $end_date, $merchant, 're-schedule-pickup');
                $data[__('received-by-pickup-man')]       = $this->eventQuery($start_date, $end_date, $merchant, 'received-by-pickup-man');
                $data[__('received_by_warehouse')]        = $this->eventQuery($start_date, $end_date, $merchant, 'received');
                $data[__('delivery-assigned')]            = $this->eventQuery($start_date, $end_date, $merchant, 'delivery-assigned');
                $data[__('re-schedule-delivery')]         = $this->eventQuery($start_date, $end_date, $merchant, 're-schedule-delivery');
                $data[__('returned-to-warehouse')]        = $this->eventQuery($start_date, $end_date, $merchant, 'returned-to-warehouse');
                $data[__('return-assigned-to-merchant')]  = $this->eventQuery($start_date, $end_date, $merchant, 'return-assigned-to-merchant');
                $data[__('returned-to-merchant')]         = $this->eventQuery($start_date, $end_date, $merchant, 'returned-to-merchant');
                $data[__('delivered')]                    = $this->eventQuery($start_date, $end_date, $merchant, 'delivered');
                $data[__('partially-delivered')]          = $this->eventQuery($start_date, $end_date, $merchant, 'partially-delivered');
                $data[__('cancelled')]                    = $this->eventQuery($start_date, $end_date, $merchant, 'cancel');
                $data[__('deleted')]                      = $this->eventQuery($start_date, $end_date, $merchant, 'deleted');
                $data[__('re-request')]                   = $this->eventQuery($start_date, $end_date, $merchant, 're-request');
            endif;

            return $data;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function eventQuery($start_date, $end_date, $merchant, $status)
    {
        $parcel_events = Parcel::query();

        $parcel_events->where('date', '>=', $start_date);
        $parcel_events->where('date', '<=', $end_date);

        if ($merchant != ''):
            $parcel_events->where('merchant_id', $merchant);
        endif;

        if ($status == "delivered") {
            return $parcel_events->whereIn('status',['delivered','delivered-and-verified'])->count();
        }

        if ($status == "returned-to-warehouse" || $status == 'return-assigned-to-merchant' || $status == 'returned-to-merchant') {
            return $parcel_events->where('is_partially_delivered', false)->where('status', $status)->count();
        }

        if ($status == "partially-delivered") {
            return $parcel_events->where('is_partially_delivered', true)->count();
        }

        return $parcel_events->where('status', $status)->count();
    }

    public function totalSummerySearch($request)
    {
        try {
            $data['start_date'] = date('Y-m-d', strtotime($request->start_date));
            $data['end_date']   = date('Y-m-d', strtotime($request->end_date));


            $data['total_parcels']          = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->count();
            $data['delivered']              = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->whereIn('status',['delivered','delivered-and-verified'])
                                                    ->count();
            $data['partially-delivered']     = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->where('is_partially_delivered', true)
                                                    ->count();

            $data['returned_to_merchant']   = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->where(function ($query){
                                                        $query->where('is_partially_delivered', false)
                                                            ->where('status', 'returned-to-merchant');
                                                    })
                                                    ->count();
            $data['cancelled']              = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->where('status', 'cancel')
                                                    ->count();
            $data['pending-return']        = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->whereIn('status', ['returned-to-warehouse','return-assigned-to-merchant','cancel','partially-delivered'])->count();
            $data['deleted']               = Parcel::where('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                                                    ->where('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                                                    ->where('status', 'deleted')
                                                    ->count();

            $data['processing']            = $data['total_parcels'] - ($data['delivered'] + $data['partially-delivered'] + $data['returned_to_merchant'] + $data['cancelled'] + $data['deleted']);

            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function profits($request)
    {
        try {
            $start  = date('Y-m-d', strtotime($request->start_date));
            $end    = date('Y-m-d', strtotime($request->end_date));

            $total_vat_income           = GovtVat::when($request->merchant != '', function ($query) use ($request){
                                                    $query->where('merchant_id', $request->merchant);
                                                })->where('date', '>=', $start)
                                                ->where('date', '<=', $end)
                                                ->where('type', 'income')
                                                ->where('parcel_id', '!=', '')->whereIn('source', ['parcel_delivery', 'parcel_return'])
                                                ->sum('amount');

            $total_vat_expense          = GovtVat::when($request->merchant != '', function ($query) use ($request){
                                                $query->where('merchant_id', $request->merchant);
                                                })->where('date', '>=', $start)
                                                ->where('date', '<=', $end)
                                                ->where('type', 'expense')
                                                ->where('parcel_id', '!=', '')->whereIn('source', ['parcel_delivery', 'parcel_return'])
                                                ->sum('amount');

            $profits['total_vat']       = $total_vat_income - $total_vat_expense;

            $return_income              = MerchantAccount::when($request->merchant != '', function ($query) use ($request){
                                                    $query->where('merchant_id', $request->merchant);
                                                })->where('date', '>=', $start)
                                                ->where('date', '<=', $end)
                                                ->where('type', 'income')
                                                ->where(function ($query){
                                                    $query->where('source','parcel_return')
                                                        ->orWhere(function ($query){
                                                            $query->where('source','vat_adjustment')
                                                                ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                                        });
                                                })
                                                ->sum('amount');
            $return_expense               = MerchantAccount::when($request->merchant != '', function ($query) use ($request){
                                                    $query->where('merchant_id', $request->merchant);
                                                })->where('date', '>=', $start)
                                                ->where('date', '<=', $end)
                                                ->where('type', 'expense')
                                                ->where(function ($query){
                                                    $query->where('source','parcel_return')
                                                        ->orWhere(function ($query){
                                                            $query->where('source','vat_adjustment')
                                                                ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                                        });
                                                })
                                                ->sum('amount');

            $profits['total_parcel_return_charge']= $return_expense - $return_income;

            $total_charge_vat            = Parcel::when($request->merchant != '', function ($query) use ($request){
                                                    $query->where('merchant_id', $request->merchant);
                                                })->where('date', '>=', $start)
                                                ->where('date', '<=', $end)
                                                ->where(function ($query){
                                                    $query->where('is_partially_delivered', true)
                                                        ->orWhereIn('status',['delivered','delivered-and-verified']);
                                                })
                                                ->sum('total_delivery_charge');
            $profits['total_charge_vat']  = $total_charge_vat + $profits['total_parcel_return_charge'];

            $total_delivery_charge_income = DeliveryManAccount::when($request->merchant != '', function ($query) use ($request){
                                                    $query->where('merchant_id', $request->merchant);
                                                })->where('date', '>=', $start)
                                                 ->where('date', '<=', $end)
                                                 ->whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
                                                 ->where('type', 'income')
                                                 ->sum('amount');

            $total_delivery_charge_expense = DeliveryManAccount::when($request->merchant != '', function ($query) use ($request){
                                                        $query->where('merchant_id', $request->merchant);
                                                    })->where('date', '>=', $start)
                                                    ->where('date', '<=', $end)
                                                    ->whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
                                                    ->where('type', 'expense')
                                                    ->sum('amount');

            $profits['total_delivery_charge'] = $total_delivery_charge_expense - $total_delivery_charge_income;

            $profits['total_fragile_charge']  = Parcel::when($request->merchant != '', function ($query) use ($request){
                                                            $query->where('merchant_id', $request->merchant);
                                                        })->where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where(function ($query){
                                                            $query->where('is_partially_delivered', true)
                                                                ->orWhereIn('status',['delivered','delivered-and-verified']);
                                                        })
                                                        ->sum('fragile_charge');
            $profits['total_packaging_charge']   = Parcel::when($request->merchant != '', function ($query) use ($request){
                                                            $query->where('merchant_id', $request->merchant);
                                                        })->where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where(function ($query){
                                                            $query->where('is_partially_delivered', true)
                                                                ->orWhereIn('status',['delivered','delivered-and-verified']);
                                                        })
                                                        ->sum('packaging_charge');

            $profits['total_profit']              = abs($profits['total_charge_vat']) - $profits['total_delivery_charge'] -  $profits['total_vat'] + $profits['total_fragile_charge'] + $profits['total_packaging_charge'];

            $profits['total_payable_to_merchant'] = Parcel::when($request->merchant != '', function ($query) use ($request){
                                                            $query->where('merchant_id', $request->merchant);
                                                        })->where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where(function ($query){
                                                            $query->where('is_partially_delivered', true)
                                                                ->orWhereIn('status',['delivered','delivered-and-verified']);
                                                        })
                                                        ->sum('price');

            $profits['total_paid_to_merchant'] = MerchantWithdraw::when($request->merchant != '', function ($query) use ($request){
                                                            $query->where('merchant_id', $request->merchant);
                                                        })->where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->whereIn('status', ['processed', 'pending'])
                                                        ->sum('amount');

            $profits['pending_payments']       = MerchantWithdraw::when($request->merchant != '', function ($query) use ($request){
                                                            $query->where('merchant_id', $request->merchant);
                                                        })->where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->whereIn('status', ['pending'])
                                                        ->sum('amount');

            $profits['total_paid_by_merchant'] = CompanyAccount::when($request->merchant != '', function ($query) use ($request){
                                                            $query->where('merchant_id', $request->merchant);
                                                        })->where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where('source', 'delivery_charge_receive_from_merchant')
                                                        ->where('type', 'income')
                                                        ->where('merchant_id', '!=', '')
                                                        ->sum('amount');

            $profits['current_payable']        = abs($profits['total_payable_to_merchant']) + $profits['total_paid_by_merchant'] - $profits['total_paid_to_merchant'] - $profits['total_charge_vat'];

            $profits['total_cash_on_delivery'] = Parcel::where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where(function ($query){
                                                            $query->where('is_partially_delivered', true)
                                                                ->orWhereIn('status',['delivered','delivered-and-verified']);
                                                        })
                                                        ->sum('price');

            $profits['total_paid_by_delivery_man'] = DeliveryManAccount::where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where('delivery_man_id', '!=', '')
                                                        ->where('source', 'cash_given_to_staff')
                                                        ->where('type', 'expense')
                                                        ->sum('amount');

            $profits['total_expense_from_account'] = CompanyAccount::where('date', '>=', $start)
                                                        ->where('date', '<=', $end)
                                                        ->where('type', 'expense')
                                                        ->where('create_type', 'user_defined')
                                                        ->sum('amount');

            $start  = $start . ' ' . '00:00:00';
            $end    = $end . ' ' . '23:59:59';

            $profits['total_bank_opening_balance'] = Account::where('created_at', '>=', $start)
                                                        ->where('created_at', '<=', $end)
                                                        ->sum('balance');
            return $profits;
        } catch (\Exception $e){
            return false;
        }
    }
}
