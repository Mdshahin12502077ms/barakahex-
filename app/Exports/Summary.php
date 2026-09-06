<?php

namespace App\Exports;

use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Merchant;
use App\Models\Parcel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Summary implements  FromView, WithTitle, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $id;
    protected $count;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function view(): View
    {
        $total_parcels                   = Parcel::where('merchant_id', $this->id)->count();

        $data['Delivered']               = Parcel::where('merchant_id', $this->id)
                                                ->whereIn('status',['delivered','delivered-and-verified'])
                                                ->count();

        $data['Partially Delivered']     = Parcel::where('merchant_id', $this->id)
                                                ->where('is_partially_delivered', true)
                                                ->count();

        $data['Returned to Merchant']    = Parcel::where('merchant_id', $this->id)
                                                ->where(function ($query){
                                                    $query->where('is_partially_delivered', false)
                                                        ->where('status', 'returned-to-merchant');
                                                })->count();

        $data['Cancelled']               = Parcel::where('merchant_id', $this->id)
                                                ->where('status', 'cancel')
                                                ->count();

        $data['Pending Return']         = Parcel::where('merchant_id', $this->id)
                                                ->whereIn('status', ['returned-to-warehouse','return-assigned-to-merchant','cancel','partially-delivered'])->count();

        $data['Deleted']                = Parcel::where('merchant_id', $this->id)
                                                ->where('status', 'deleted')
                                                ->count();

        $data['Processing']             = $total_parcels - ($data['Delivered'] + $data['Partially Delivered'] + $data['Returned to Merchant'] + $data['Cancelled'] + $data['Deleted']);
        $data['Total']                  = $total_parcels;

        $return_income                  = MerchantAccount::where('merchant_id', $this->id)
                                                ->where('type', 'income')
                                                ->whereHas('parcel',function ($q){
                                                    $q->where('is_partially_delivered',false);
                                                })
                                                ->where(function ($query){
                                                    $query->where('source','parcel_return')
                                                        ->orWhere(function ($query){
                                                            $query->where('source','vat_adjustment')
                                                                ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                                        });
                                                })
                                                ->sum('amount');
        $return_expense                = MerchantAccount::where('merchant_id', $this->id)
                                                ->where('type', 'expense')
                                                ->whereHas('parcel',function ($q){
                                                    $q->where('is_partially_delivered',false);
                                                })
                                                ->where(function ($query){
                                                    $query->where('source','parcel_return')
                                                        ->orWhere(function ($query){
                                                            $query->where('source','vat_adjustment')
                                                                ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                                        });
                                                })
                                                ->sum('amount');

        $partially_return_income        = MerchantAccount::where('merchant_id', $this->id)
                                                ->where('type', 'income')
                                                ->whereHas('parcel',function ($q){
                                                    $q->where('is_partially_delivered',true);
                                                })
                                                ->where(function ($query){
                                                    $query->where('source','parcel_return')
                                                        ->orWhere(function ($query){
                                                            $query->where('source','vat_adjustment')
                                                                ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                                        });
                                                })
                                                ->sum('amount');
        $partially_return_expense        = MerchantAccount::where('merchant_id', $this->id)
                                                ->where('type', 'expense')
                                                ->whereHas('parcel',function ($q){
                                                    $q->where('is_partially_delivered',true);
                                                })
                                                ->where(function ($query){
                                                    $query->where('source','parcel_return')
                                                        ->orWhere(function ($query){
                                                            $query->where('source','vat_adjustment')
                                                                ->whereIn('details',['govt_vat_for_parcel_return','govt_vat_for_parcel_return_reversed']);
                                                        });
                                                })
                                                ->sum('amount');

        $profits['total_parcel_return_charge']    = $return_expense - $return_income;
        $profits['total_partially_return_charge'] = $partially_return_expense - $partially_return_income;

        $profits['total_partial_delivery_charge_vat']   = Parcel::where('merchant_id', $this->id)
                                                        ->where('is_partially_delivered', true)
                                                        ->sum('total_delivery_charge');

        $profits['total_delivery_charge_vat']    = Parcel::where('merchant_id', $this->id)
                                                    ->whereIn('status',['delivered','delivered-and-verified'])
                                                    ->sum('total_delivery_charge');

        $profits['total_charge']                = $profits['total_parcel_return_charge'] + $profits['total_partially_return_charge'] + $profits['total_partial_delivery_charge_vat'] +$profits['total_delivery_charge_vat'];

        $profits['total_payable_to_merchant'] = Parcel::where('merchant_id', $this->id)
                                                    ->where(function ($query){
                                                        $query->where('is_partially_delivered', true)
                                                            ->orWhereIn('status',['delivered','delivered-and-verified']);
                                                    })
                                                    ->sum('price');

        $profits['total_paid_to_merchant']   = MerchantWithdraw::where('merchant_id', $this->id)
                                                    ->whereIn('status', ['processed'])
                                                    ->sum('amount');

        $profits['pending_payments']       = MerchantWithdraw::where('merchant_id', $this->id)
                                                    ->whereIn('status', ['pending','approved'])
                                                    ->sum('amount');


        $profits['total_paid_by_merchant'] = CompanyAccount::where('merchant_id', $this->id)
                                                    ->where('source', 'delivery_charge_receive_from_merchant')
                                                    ->where('type', 'income')
                                                    ->where('merchant_id', '!=', '')
                                                    ->sum('amount');
        $merchant = Merchant::find($this->id);
        $profits['available_balance'] = $merchant->balance($this->id);

        $profits['current_payable']        = abs($profits['total_payable_to_merchant']) + $profits['total_paid_by_merchant'] - $profits['total_paid_to_merchant'] -
                                             $profits['total_delivery_charge_vat'] - $profits['total_parcel_return_charge'] - $profits['total_partially_return_charge'] - $profits['total_partial_delivery_charge_vat'];

        return view('admin.exports.summary',compact('data',  'profits'));
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            13   => ['font' => ['bold' => true]],
            15   => ['font' => ['bold' => true]],
            16   => ['font' => ['bold' => true]],
            18   => ['font' => ['bold' => true]],
            23   => ['font' => ['bold' => true]],
            26   => ['font' => ['bold' => true]],
            27   => ['font' => ['bold' => true]],

        ];
    }
}
