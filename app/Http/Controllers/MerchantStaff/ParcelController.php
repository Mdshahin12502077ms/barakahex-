<?php

namespace App\Http\Controllers\MerchantStaff;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Charge;
use App\Models\Parcel;
use App\Models\Shop;
use App\Models\CodCharge;
use App\Models\ThirdParty;
use Illuminate\Http\Request;
use App\Exports\FilteredParcel;
use App\Exports\Merchant\MerchantFilteredParcel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\Merchant\ParcelsDataTable;
use App\Repositories\Interfaces\ParcelInterface;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Admin\Parcel\ParcelStoreRequest;
use App\Http\Requests\Admin\Parcel\ParcelUpdateRequest;

class ParcelController extends Controller
{
    protected $parcels;

    public function __construct(ParcelInterface $parcels)
    {
        $this->parcels = $parcels;
    }

    public function index(ParcelsDatatable $dataTable, Request $request)
    {
        $charges        = Charge::all();
        $cod_charges    = CodCharge::all();
        $pn             = isset($request->pn) ? $request->pn : '';
        $parcels        = $this->paginateForMerchant(\Config::get('parcel.parcel_merchant_paginate'),$pn);
        return $dataTable
        ->render('merchant.parcel.index', compact('parcels','cod_charges','charges'));
    }

    public function paginateForMerchant($limit,$pn = '')
    {
        return Parcel::where('merchant_id', Sentinel::getUser()->merchant_id)
            ->when(!hasPermission('all_parcel'), function ($query){
                $query->where('user_id',Sentinel::getUser()->id);
            })->where('parcel_no','like','%' . $pn .'%')
            ->orderBy('id', 'desc')->paginate($limit);
    }

    public function create()
    {
        if (@settingHelper('preferences')->where('title','create_parcel')->first()->merchant):
            $shops          = Sentinel::getUser()->staffMerchant->shops->whereIn('id',Sentinel::getUser()->shops);
            $charges        = Charge::all();
            $cod_charges    = CodCharge::all();
            return view('merchant.parcel.create', compact('charges', 'cod_charges','shops'));
        else:
            return back()->with('danger', __('service_unavailable'));
        endif;
    }

    public function store(ParcelStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if (@settingHelper('preferences')->where('title','create_parcel')->first()->merchant):
                if($this->parcels->store($request)):
                    return redirect()->route('merchant.staff.parcel')->with('success', __('created_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return redirect()->route('merchant.staff.parcel')->with('danger', __('service_unavailable'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }



    public function duplicate($id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if (@settingHelper('preferences')->where('title','create_parcel')->first()->merchant):
                $parcel = Parcel::find($id);
                $shops = Sentinel::getUser()->staffMerchant->shops->whereIn('id',Sentinel::getUser()->shops);
                $default_shop = Sentinel::getUser()->staffMerchant->shops()->where('default',1)->first();
                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                return view('merchant.parcel.create', compact('parcel', 'charges', 'cod_charges','shops','default_shop'));
            else:
                return back()->with('danger', __('service_unavailable'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function detail($id)
    {
        try {
            $parcel = Parcel::with('merchant.user','events','branch')->find($id);
            if(($parcel->merchant->id == Sentinel::getUser()->merchant_id && hasPermission('all_parcel')) || ($parcel->merchant->id == Sentinel::getUser()->merchant_id && $parcel->user_id == Sentinel::getUser()->id)):
                $charges        = Charge::all();
                $cod_charges    = CodCharge::all();
                return view('merchant.parcel.detail', compact('parcel','cod_charges','charges'));
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function print($id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = Parcel::find($id);
            if(($parcel->merchant->id == Sentinel::getUser()->merchant_id && hasPermission('all_parcel')) || ($parcel->merchant->id == Sentinel::getUser()->merchant_id && $parcel->user_id == Sentinel::getUser()->id)):
                $charges        = Charge::all();
                $cod_charges    = CodCharge::all();
                return view('merchant.parcel.print', compact('parcel','cod_charges','charges'));
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        $shops          = Sentinel::getUser()->staffMerchant->shops->whereIn('id',Sentinel::getUser()->shops);
        $parcel         = $this->parcels->get($id);
        $charges        = Charge::all();
        $cod_charges    = CodCharge::all();

        if(($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
            && ($parcel->merchant->id == Sentinel::getUser()->merchant_id && hasPermission('all_parcel')) || ($parcel->merchant->id == Sentinel::getUser()->merchant_id && $parcel->user_id == Sentinel::getUser()->id)):
            return view('merchant.parcel.edit', compact('parcel', 'charges', 'cod_charges','shops'));
        else:
            return back()->with('danger', __('you_are_not_allowed_to_update_this_parcel'));
        endif;
    }

    public function update(ParcelUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->parcels->update($request)):
                return redirect()->route('merchant.staff.parcel')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function parcelDelete(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (($parcel->merchant->id == Sentinel::getUser()->merchant_id && hasPermission('all_parcel')) || ($parcel->merchant->id == Sentinel::getUser()->merchant_id && $parcel->user_id == Sentinel::getUser()->id)):
                if ($parcel->status == 'deleted'):
                    return back()->with('danger', __('this_parcel_has_already_been_deleted'));
                endif;

                if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup'):
                    if($this->parcels->parcelDelete($request)):
                        return redirect()->route('merchant.staff.parcel')->with('success', __('deleted_successfully'));
                    else:
                        return back()->with('danger', __('something_went_wrong_please_try_again'));
                    endif;
                else:
                    return back()->with('danger', __('this_parcel_can_not_be_deleted'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelCancel(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (in_array($parcel->status, \Config::get('parcel.merchant_cancel_parcel'))):
                if (($parcel->merchant->id == Sentinel::getUser()->merchant_id && hasPermission('all_parcel'))
                    || ($parcel->merchant->id == Sentinel::getUser()->merchant_id && $parcel->user_id == Sentinel::getUser()->id)):
                    if ($parcel->status == 'cancel'):
                        return back()->with('danger', __('this_parcel_has_already_been_cancelled'));
                    endif;

                    if($this->parcels->parcelCancel($request)):
                        return redirect()->route('merchant.staff.parcel')->with('success', __('updated_successfully'));
                    else:
                        return back()->with('danger', __('something_went_wrong_please_try_again'));
                    endif;
                else:
                    return back()->with('danger', __('access_denied'));
                endif;
            else:
                return back()->with('danger', __('you_are_not_allowed_to_cancel_this_parcel'));
            endif;

        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReRequest(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if($parcel->status == 'cancel' && ($parcel->merchant->id == Sentinel::getUser()->merchant_id && hasPermission('all_parcel')) || ($parcel->merchant->id == Sentinel::getUser()->merchant_id && $parcel->user_id == Sentinel::getUser()->id)):
                if($this->parcels->parcelStatusUpdate($parcel->id, 're-request', $request->note)):
                    return redirect()->route('merchant.staff.parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('you_are_not_allowed_to_update_this_parcel'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelFiltering($slug)
    {
        $charges        = Charge::all();
        $cod_charges    = CodCharge::all();
        $pn             = isset($request->pn) ? $request->pn : '';
        $parcels        = Parcel::where('merchant_id', Sentinel::getUser()->merchant_id)
                                ->when(!hasPermission('all_parcel'), function ($query){
                                    $query->where('user_id',Sentinel::getUser()->id);
                                })->when($slug == 'pending-return', function ($q){
                                    $q->whereIn('status', ['returned-to-warehouse','return-assigned-to-merchant','cancel','partially-delivered']);
                                })
                                ->when($slug == 'partially-delivered', function ($q) {
                                    $q->whereIn('status', ['partially-delivered','returned-to-merchant'])
                                        ->where('is_partially_delivered', '=', 1);
                                })
                                ->when($slug != 'pending-return' && $slug != 'partially-delivered', function ($q) use($slug){
                                    $q->where('status', $slug);
                                })->where('parcel_no','like','%' . $pn .'%')
                                ->orderBy('id', 'desc')
                                ->paginate(\Config::get('parcel.parcel_merchant_paginate'));
        return view('merchant.parcel.index', compact('parcels','charges','cod_charges', 'slug','pn'));
    }

    public function shop(Request $request){
        $shop                       = Shop::with('branch')->find($request->shop_id);
        dd($shop);
        $data['shop_pickup_branch'] = $shop->branch->name ?? '';
        $data['shop_phone_number']  = $shop->shop_phone_number;
        $data['address']            = $shop->address;
        return response()->json($data);
    }

    public function filter(Request $request)
    {
        $query = Parcel::query();

        $pn             = isset($request->pn) ? $request->pn : '';

        $query->where('merchant_id', Sentinel::getUser()->merchant_id);
        $query->where('parcel_no','like','%' . $pn .'%');

        if(!hasPermission('all_parcel')){
            $query->where('user_id', Sentinel::getUser()->id);
        }

        if ($request->created_from != "") {
            $created_from = date("Y-m-d", strtotime($request->created_from));
            $query->whereDate('created_at', '>=', "{$created_from}%");
            if ($request->created_to != ""){
                $created_to = date("Y-m-d", strtotime($request->created_to));
                $query->whereDate('created_at', '<=', "{$created_to}%");
            }
        }

        if ($request->customer_name != "") {
            $query->where('customer_name', 'LIKE', "%{$request->customer_name}%");
        }

        if ($request->customer_invoice_no != "") {
            $query->where('customer_invoice_no', 'LIKE', "%{$request->customer_invoice_no}%");
        }

        if ($request->phone_number != "") {
            $query->where('customer_phone_number', $request->phone_number);
        }

        if ($request->status != "any") {
            $query->when($request->status == 'pending-return', function ($q){
                $q->whereIn('status', ['returned-to-warehouse','return-assigned-to-merchant','cancel','partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered','returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use($request){
                    $q->where('status', $request->status);
                });
        }

        if ($request->weight != "any") {
            $query->where('weight', $request->weight);
        }

        if ($request->parcel_type != "any") {
            $query->where('parcel_type', $request->parcel_type);
        }

        if ($request->location != "any") {
            $query->where('location', $request->location);
        }

        if ($request->pickup_date != "") {
            $pickup_date = date("Y-m-d", strtotime($request->pickup_date));
            $query->where('pickup_date', 'LIKE', "%{$pickup_date}%");
        }

        if ($request->delivery_date != "") {
            $delivery_date = date("Y-m-d", strtotime($request->delivery_date));
            $query->where('delivery_date', 'LIKE', "%{$delivery_date}%");
        }

        if ($request->delivered_date != "") {
            $delivered_date = date("Y-m-d", strtotime($request->delivered_date));
            $query->whereHas('events', function ($inner_query) use ($delivered_date) {
                $inner_query->where('title', 'parcel_delivered_event');
                $inner_query->where('created_at', 'LIKE', "%{$delivered_date}%");
            });
        }

        if ($request->has('download')):
            $file_name = 'Filtered Parcels '.date('Y-m-d-s').'.xlsx';
            return Excel::download(new FilteredParcel($query), $file_name);
        endif;

        $parcels = $query->latest()->paginate(\Config::get('parcel.parcel_merchant_paginate'));

        $charges        = Charge::all();
        $cod_charges    = CodCharge::all();
        return view('merchant.parcel.index', compact('parcels','cod_charges','charges','pn'));

    }


    public function getParcelDownload(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $charges        = Charge::all();
            $cod_charges    = CodCharge::all();
            $third_parties  = ThirdParty::where('status', true)->orderBy('name')->get();
            if (Sentinel::getUser()->user_type == 'merchant_staff') {
                $query      = Parcel::where('merchant_id', Sentinel::getUser()->merchant_id);
            }

            if (Sentinel::getUser()->user_type == 'merchant') {
                $query      = Parcel::where('merchant_id', Sentinel::getUser()->merchant->id)
                    ->where('parcel_no', 'like', '%' . $pn . '%');
            }

            if ($request->merchant_id) {
                $query->where('merchant_id', $request->merchant_id);
            }
            if ($request->phone_number) {
                $query->whereHas('merchant', function ($inner_query) use ($request) {
                    $inner_query->where('phone_number', 'LIKE', "%{$request->phone_number}%");
                });
            }
            if ($request->customer_invoice_no) {
                $query->where('customer_invoice_no', 'LIKE', "%{$request->customer_invoice_no}%");
            }
            if ($request->created_at != "") {
                $query->when($request->created_at ?? false, function ($query, $created_at) {
                    $dateRange = $this->parseDate($created_at);
                        $query->whereBetween('created_at', $dateRange);
                });
            }
            if ($request->pickup_date) {
                $pickup_date = date("Y-m-d", strtotime($request->pickup_date));
                $query->where('pickup_date', 'LIKE', "%{$pickup_date}%");
            }
            if ($request->delivery_date) {
                $delivery_date = date("Y-m-d", strtotime($request->delivery_date));
                $query->where('delivery_date', 'LIKE', "%{$delivery_date}%");
            }
            if ($request->delivered_date) {
                $delivered_date = date("Y-m-d", strtotime($request->delivered_date));
                $query->whereHas('events', function ($inner_query) use ($delivered_date) {
                    $inner_query->where('title', 'parcel_delivered_event');
                    $inner_query->where('created_at', 'LIKE', "%{$delivered_date}%");
                });
            }

            if ($request->status) {
                $query->when($request->status == 'pending-return', function ($q){
                    $q->whereIn('status', ['returned-to-warehouse','return-assigned-to-merchant','cancel','partially-delivered']);
                })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered','returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use($request){
                    $q->where('status', $request->status);
                });
            }
            if ($request->weight) {
                $query->where('weight', $request->weight);
            }
            if ($request->parcel_type) {
                $query->where('parcel_type', $request->parcel_type);
            }
            if ($request->location) {
                $query->where('location', $request->location);
            }
            $file_name = 'Filtered Parcels '.date('Y-m-d-s').'.xlsx';
            return Excel::download(new FilteredParcel($query), $file_name);
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }
}
