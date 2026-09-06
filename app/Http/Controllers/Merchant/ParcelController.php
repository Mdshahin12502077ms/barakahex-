<?php

namespace App\Http\Controllers\Merchant;

use App\Exports\ClosingReport;
use App\Exports\Merchant\MerchantFilteredParcel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Parcel\ParcelStoreRequest;
use App\Http\Requests\Admin\Parcel\ParcelUpdateRequest;
use App\Models\Charge;
use App\Models\CodCharge;
use App\Models\Parcel;
use App\Models\City;
use App\Models\District;
use App\Models\Thana;
use App\Repositories\Interfaces\DeliveryManInterface;
use App\Repositories\Interfaces\ParcelInterface;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use App\DataTables\Merchant\ParcelsDataTable;
use Maatwebsite\Excel\Facades\Excel;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Carbon;

class ParcelController extends Controller
{
    use ApiReturnFormatTrait;

    protected $parcels;
    protected $delivery_man;

    public function __construct(ParcelInterface $parcels, DeliveryManInterface $delivery_man)
    {
        $this->parcels = $parcels;
        $this->delivery_man = $delivery_man;
    }

    private function paginateForMerchant($limit, $pn = '')
    {
        return Parcel::where('merchant_id', Sentinel::getUser()->merchant->id)->where('parcel_no', 'like', '%' . $pn . '%')->orderBy('id', 'desc')->paginate($limit);
    }

    public function index(ParcelsDataTable $dataTable, Request $request)
    {
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $pn = isset($request->pn) ? $request->pn : '';
        $parcels = $this->paginateForMerchant(\Config::get('parcel.parcel_merchant_paginate'), $pn);
        return $dataTable->render('merchant.parcel.index', compact('parcels', 'cod_charges', 'charges', 'pn'));
    }

    public function create()
    {
        if (true):
            $shops = Sentinel::getUser()->merchant->shops;
            $default_shop = Sentinel::getUser()->merchant->shops()->where('default', 1)->first();
            $charges = Charge::all();
            $cod_charges = CodCharge::all();
            $districts = District::with(['thanas' => function($q) { $q->where('status', 'active')->orderBy('name'); }])->active()->orderBy('name')->get();
            $city = $districts;


            return view('merchant.parcel.create', compact('charges', 'cod_charges', 'shops', 'default_shop', 'districts', 'city'));
        else:
            return back()->with('danger', __('service_unavailable'));
        endif;
    }
    
    public function thanabook(Request $request){
        $district_id = $request->district_id ?? $request->city_id;
        $thanas = Thana::where('district_id', $district_id)->where('status', 'active')->orderBy('name')->get();

        if ($request->ajax() && $request->wantsJson()) {
            return response()->json($thanas);
        }

        $output = "<option value=''>Select Thana</option>";
        foreach($thanas as $thana){
            $output .= "<option value='".$thana->id."'>".$thana->name."</option>";
        }
        return response($output);
    }

    public function store(ParcelStoreRequest $request)
    {

        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->merchant):
                if ($this->parcels->store($request)):
                    return redirect()->back()->with('success', __('created_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return redirect()->back()->with('danger', __('service_unavailable'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function edit($id)
    {
        $shops = Sentinel::getUser()->merchant->shops;
        $default_shop = Sentinel::getUser()->merchant->shops()->where('default', 1)->first();
        $parcel = $this->parcels->get($id);
        $charges = Charge::all();
        $cod_charges = CodCharge::all();

        if (($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup') && $parcel->merchant->id == Sentinel::getUser()->merchant->id):
            return view('merchant.parcel.edit', compact('parcel', 'charges', 'cod_charges', 'shops', 'default_shop'));
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
        try {
            if ($this->parcels->update($request)):
                return redirect()->route('merchant.parcel')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function filter(Request $request)
    {
        $query = Parcel::query();

        $pn = isset($request->pn) ? $request->pn : '';

        $query->where('merchant_id', Sentinel::getUser()->merchant->id);
        $query->where('parcel_no', 'like', '%' . $pn . '%');

        if ($request->created_from != "") {
            $created_from = date("Y-m-d", strtotime($request->created_from));
            $query->whereDate('created_at', '>=', "{$created_from}%");
            if ($request->created_to != "") {
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
            $query->when($request->status == 'pending-return', function ($q) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use ($request) {
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
            $file_name = 'Filtered Parcels ' . date('Y-m-d-s') . '.xlsx';
            return Excel::download(new MerchantFilteredParcel($query), $file_name);
        endif;

        $parcels = $query->latest()->paginate(\Config::get('parcel.parcel_merchant_paginate'));

        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        return view('merchant.parcel.index', compact('parcels', 'cod_charges', 'charges', 'pn'));

    }
    public function detail($id)
    {
        try {
            $parcel = Parcel::with([
                'merchant.user',
                'events',
                'branch',
                'movements.fromBranch',
                'movements.toBranch',
                'movements.sender',
                'movements.receiver',
                'movements.deliveryMan.user'
            ])->find($id);
            if ($parcel->merchant->id == Sentinel::getUser()->merchant->id):
                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                return view('merchant.parcel.detail', compact('parcel', 'cod_charges', 'charges'));
            else:
                return back()->with('danger', __('you_are_not_allowed'));
            endif;
        } catch (\Exception $e) {
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
            if ($parcel->merchant->id == Sentinel::getUser()->merchant->id):
                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                return view('merchant.parcel.print', compact('parcel', 'cod_charges', 'charges'));
            else:
                return back()->with('danger', __('you_are_not_allowed'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelStatusUpdate($id, $status)
    {
        if (isDemoMode()) {

            $success = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 500,
                'message' => $success,
            ]);
        }
        try {
            $parcel = $this->parcels->get($id);
            if (($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup' || $parcel->status == 'cancel') && $parcel->merchant->id == Sentinel::getUser()->merchant->id):
                if ($this->parcels->parcelStatusUpdate($id, $status, '')):
                    $success[0] = __('updated_successfully');
                    $success[1] = 'success';
                    $success[2] = __('updated');
                    return response()->json($success);
                else:
                    $success[0] = __('something_went_wrong_please_try_again');
                    $success[1] = 'error';
                    $success[2] = __('oops');
                    return response()->json($success);
                endif;
            else:
                return back()->with('danger', __('you_are_not_allowed_to_update_this_parcel'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function duplicate($id)
    {
        try {
            if (isDemoMode()) {
                Toastr::error(__('this_function_is_disabled_in_demo_server'));
                return back();
            }

            if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->merchant):
                $parcel = Parcel::find($id);
                $shops = Sentinel::getUser()->merchant->shops;
                $default_shop = Sentinel::getUser()->merchant->shops()->where('default', 1)->first();
                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                return view('merchant.parcel.create', compact('parcel', 'charges', 'cod_charges', 'shops', 'default_shop'));
            else:
                return back()->with('danger', __('service_unavailable'));
            endif;
        } catch (\Exception $e) {
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
            if ($parcel->merchant->id == Sentinel::getUser()->merchant->id && in_array($parcel->status, \Config::get('parcel.merchant_cancel_parcel'))):
                if ($parcel->status == 'cancel'):
                    return back()->with('danger', __('this_parcel_has_already_been_cancelled'));
                endif;

                if ($this->parcels->parcelCancel($request)):
                    return redirect()->route('merchant.parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('you_are_not_allowed_to_cancel_this_parcel'));
            endif;
        } catch (\Exception $e) {
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
            if ($parcel->merchant->id == Sentinel::getUser()->merchant->id):
                if ($parcel->status == 'deleted'):
                    return back()->with('danger', __('this_parcel_has_already_been_deleted'));
                endif;

                if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup'):
                    if ($this->parcels->parcelDelete($request)):
                        return redirect()->route('merchant.parcel')->with('success', __('deleted_successfully'));
                    else:
                        return back()->with('danger', __('something_went_wrong_please_try_again'));
                    endif;
                else:
                    return back()->with('danger', __('this_parcel_can_not_be_deleted'));
                endif;
            else:
                return back()->with('danger', __('you_are_not_allowed'));
            endif;
        } catch (\Exception $e) {
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
            if ($parcel->status == 'cancel' && $parcel->merchant->id == Sentinel::getUser()->merchant->id):
                if ($this->parcels->parcelStatusUpdate($parcel->id, 're-request', $request->note)):
                    return redirect()->route('merchant.parcel')->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('you_are_not_allowed_to_update_this_parcel'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelFiltering(ParcelsDataTable $dataTable, $slug, Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $pn = isset($request->pn) ? $request->pn : '';
        $parcels = $this->paginateForMerchant(\Config::get('parcel.parcel_merchant_paginate'), $pn);

        $request->merge(['status' => $slug]);
        return $dataTable->render('merchant.parcel.index', compact('parcels', 'charges', 'cod_charges', 'slug', 'pn'));
    }

    public function track($id)
    {
        try {
            $parcel = Parcel::where('parcel_no', $id)->latest()->first();

            if (!blank($parcel)):
                $data['parcel_no'] = $parcel->parcel_no;
                $data['status'] = __($parcel->status);

                $merchant['merchan_name'] = $parcel->merchant->user->first_name . ' ' . $parcel->merchant->user->last_name;
                $merchant['merchant_company'] = $parcel->merchant->company;
                $merchant['phone_number'] = $parcel->merchant->phone_number;
                $merchant['address'] = $parcel->merchant->address;
                $merchant['email'] = $parcel->merchant->user->email;
                $merchant['created'] = date('M d, Y g:i a', strtotime($parcel->created_at));
                $merchant['parcel_type'] = __($parcel->parcel_type);
                $merchant['total_charge'] = format_price(__($parcel->total_delivery_charge));
                $merchant['pickup_person'] = @$parcel->pickupMan->user['first_name'] . ' ' . @$parcel->pickupMan->user['last_name'];
                $merchant['delivery_person'] = @$parcel->deliveryMan->user['first_name'] . ' ' . @$parcel->deliveryMan->user['last_name'];
                $merchant['return_delivery_person'] = @$parcel->returnDeliveryMan->user['first_name'] . ' ' . @$parcel->returnDeliveryMan->user['last_name'];
                if (
                    $parcel->status != 'pending' && $parcel->status
                    != 'pickup-assigned' && $parcel->status != 're-schedule-pickup'
                    && $parcel->status != 'received-by-pickup-man'
                ):
                    if (!blank($parcel->branch)):
                        $merchant['branch'] = @$parcel->branch->name . ' (' . @$parcel->branch->address . ')';
                    else:
                        $merchant['branch'] = '';
                    endif;
                endif;
                if ($parcel->status == 'transferred-to-branch'):
                    $merchant['transferring_to_branch'] = @$parcel->transferToBranch->name . ' (' . @$parcel->transferToBranch->address . ')';
                endif;
                $merchant['pickup'] = date('M d, Y', strtotime($parcel->pickup_date));
                $merchant['delivery'] = date('M d, Y', strtotime($parcel->delivery_date));
                $merchant['pickup'] = date('M d, Y', strtotime($parcel->pickup_date));

                if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified'):
                    $merchant['delivered_at'] = date('M d, Y g:i A', strtotime($parcel->updated_at));
                endif;

                $customer['id'] = $parcel->parcel_no;
                $customer['invno'] = $parcel->customer_invoice_no;
                $customer['customer_name'] = $parcel->customer_name;
                $customer['customer_mobile_no'] = $parcel->customer_phone_number;
                $customer['customer_mobile_no'] = $parcel->customer_address;
                $customer['location'] = __($parcel->location);
                $customer['note'] = __($parcel->note);
                $customer['weight'] = $parcel->weight . ' ' . __('kg');
                $customer['total_cod'] = format_price($parcel->price);

                $events = $parcel->events;

                foreach ($events as $event):
                    $event['event'] = $event->title;
                    $event['title'] = __($event->title);
                    $event['date'] = date('d M Y', strtotime($event->created_at));
                    $event['time'] = date('h:i a', strtotime($event->created_at));
                    $event['processed_by'] = $event->user['first_name'] . ' ' . $event->user['last_name'];
                    $event['pickup_man'] = @$event->pickupPerson->user->first_name . ' ' . @$event->pickupPerson->user->last_name;
                    $event['pickup_man_phone'] = @$event->pickupPerson->phone_number ?? '';
                    $event['delivery_man'] = @$event->deliveryPerson->user->first_name . ' ' . @$event->deliveryPerson->user->last_name;
                    $event['delivery_man_phone'] = @$event->deliveryPerson->phone_number ?? '';
                    $event['return_delivery_man'] = @$event->returnPerson->user->first_name . ' ' . @$event->returnPerson->user->last_name;
                    $event['return_delivery_man_phone'] = @$event->returnPerson->phone_number ?? '';
                    $branch = '';
                    if (!blank($event->branch)):
                        $branch = @$event->branch->name . ' (' . @$event->branch->address . ')';
                    endif;
                    $event['note'] = $event->cancel_note != '' ? $event->cancel_note : '';

                    unset($event->user);
                    unset($event->pickupPerson);
                    unset($event->deliveryPerson);
                    unset($event->created_at);
                    unset($event->updated_at);
                    unset($event->reverse_status);
                    unset($event->pickup_man_id);
                    unset($event->delivery_man_id);
                    unset($event->user_id);
                    unset($event->parcel_id);
                    unset($event->return_delivery_man_id);
                    unset($event->branch);

                    $event['branch'] = $branch;
                endforeach;

                $data['merchant'] = $merchant;
                $data['customer'] = $customer;
                $data['events'] = $events;

                return $this->responseWithSuccess(__('successfully_found'), $data, 200);
            else:
                return $this->responseWithError(__('parcel_not_found'), [], 404);
            endif;

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function download()
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $merchant = Sentinel::getUser()->merchant;
            $file_name = $merchant->company . ' ' . '- Closing ' . date('Y-m-d') . '.xlsx';
            return Excel::download(new ClosingReport($merchant->id), $file_name);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function getParcelDownload(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $query = $this->buildFilteredQuery($request);
            $file_name = 'Filtered Parcels ' . date('Y-m-d-s') . '.xlsx';
            return Excel::download(new MerchantFilteredParcel($query), $file_name);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }
    private function parseDate($date_range)
    {
        $dates = explode('to', $date_range);

        if (count($dates) == 1) {
            $dates[1] = $dates[0];
        }

        $start_date = trim($dates[0]);
        $end_date = trim($dates[1]);

        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';

        return [
            Carbon::parse($start_date)->format('Y-m-d H:s:i'),
            Carbon::parse($end_date)->format('Y-m-d H:s:i'),
        ];
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = Parcel::query();

        $query->where('merchant_id', Sentinel::getUser()->merchant->id);
        if (!hasPermission('read_all_parcel')) {
            $query->where(function ($q) {
                $q->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', \Sentinel::getUser()->branch_id);
            });
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
        if ($request->created_date != "") {
            $query->when($request->created_date ?? false, function ($query, $created_date) {
                $dateRange = $this->parseDate($created_date);
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
        if ($request->pickup_man_id) {
            $query->where('pickup_man_id', $request->pickup_man_id);
        }
        if ($request->delivery_man_id) {
            $query->where('delivery_man_id', $request->delivery_man_id);
        }
        if ($request->third_party_id) {
            $query->where('third_party_id', $request->third_party_id);
        }

        if ($request->status) {
            $query->when($request->status == 'pending-return', function ($q) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use ($request) {
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
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->pickup_branch_id) {
            $query->when($request->pickup_branch_id == 'pending', function ($search) {
                $search->where('pickup_branch_id', null);
            })->when($request->pickup_branch_id != 'pending', function ($search) use ($request) {
                $search->where('pickup_branch_id', $request->pickup_branch_id);
            });
        }
        return $query;
    }

}
