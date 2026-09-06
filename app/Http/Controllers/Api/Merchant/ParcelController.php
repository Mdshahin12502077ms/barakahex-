<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use App\Http\Resources\Api\ParcelResource;
use App\Http\Resources\Api\ParcelDetailResource;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendMailTrait;
use App\Exports\Merchant\MerchantFilteredParcel;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Interfaces\ParcelInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParcelsImport;
use Carbon\Carbon;
use JWTAuth;
use Response;
use File;
use DB;

class ParcelController extends Controller
{
    use ApiReturnFormatTrait;

    protected $parcelRepo;


    public function __construct(ParcelInterface $parcelRepo)
    {

        $this->parcelRepo     = $parcelRepo;

    }
    public function allParcel(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user       = jwtUser();
            $query      = Parcel::query();
            if ($user->user_type == 'merchant_staff') {
                $query->where('merchant_id', $user->merchant_id);
                $userPermissions = $user->permissions;

                if (!in_array('all_parcel', $userPermissions)) {
                    $query->whereHas('shop', function ($q) use ($user) {
                        $q->whereIn('id', $user->shops);
                    });
                }
            }

            if ($user->user_type == 'merchant') {
                $query->where('merchant_id', $user->merchant->id);
            }
            $query->when($request->filled('customer_name'), function ($query) use ($request) {
                $query->where('customer_name', 'like', '%' . $request->input('customer_name') . '%');
            })
            ->when($request->filled('customer_invoice_no'), function ($query) use ($request) {
                $query->where('customer_invoice_no', $request->input('customer_invoice_no'));
            })
            ->when($request->filled('phone_number'), function ($query) use ($request) {
                $query->where('customer_phone_number', $request->input('phone_number'));
            })
            ->when($request->filled('created_from'), function ($query) use ($request) {
                $created_from = \Carbon\Carbon::parse($request->input('created_from'))->format('Y-m-d');
                $query->whereDate('created_at', '>=', $created_from);
                if ($request->filled('created_to')) {
                    $created_to = \Carbon\Carbon::parse($request->input('created_to'))->format('Y-m-d');
                    $query->whereDate('created_at', '<=', $created_to);
                }
            })
            ->when($request->filled('pickup_date'), function ($query) use ($request) {
                $pickup_date = \Carbon\Carbon::parse($request->input('pickup_date'))->format('Y-m-d');
                $query->whereDate('pickup_date', $pickup_date);
            })
            ->when($request->filled('delivery_date'), function ($query) use ($request) {
                $delivery_date = \Carbon\Carbon::parse($request->input('delivery_date'))->format('Y-m-d');
                $query->whereDate('delivery_date', $delivery_date);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('weight'), function ($query) use ($request) {
                $query->where('weight', $request->input('weight'));
            })
            ->when($request->filled('parcel_type'), function ($query) use ($request) {
                $query->where('parcel_type', $request->input('parcel_type'));
            })
            ->when($request->filled('location'), function ($query) use ($request) {
                $query->where('location', $request->input('location'));
            });

            $parcel = $query->latest()->paginate(10);



            $data = [
                'parcel' => ParcelResource::collection($parcel),
                'paginate' => [
                    'total'             => $parcel->total(),
                    'current_page'      => $parcel->currentPage(),
                    'per_page'          => $parcel->perPage(),
                    'last_page'         => $parcel->lastPage(),
                    'prev_page_url'     => $parcel->previousPageUrl(),
                    'next_page_url'     => $parcel->nextPageUrl(),
                    'path'              => $parcel->path(),
                ],
            ];

            return $this->responseWithSuccess('parcel_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function submitParcel(Request $request, $id = null): \Illuminate\Http\JsonResponse
    {

        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }

        $user                  = jwtUser();
        $request['merchant']   = $user->merchant->id ?? $user->merchant_id;
        $request['created_by'] = $user->id;

        $validator = Validator::make($request->all(), [
            'merchant'              => 'required',
            'customer_name'         => 'required',
            'customer_invoice_no'   => 'required',
            'customer_phone_number' => 'required|between:8,30',
            'customer_address'      => 'required',
            'parcel_type'           => 'required',
            'weight'                => 'required',
            'price'                 => 'required|numeric',
            'selling_price'         => 'numeric',
        ]);


        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            if ($id) {
                $parcels           = Parcel::findOrFail($id);
                $request['id']     = $parcels->id;

                $this->parcelRepo->update($request);
                return $this->responseWithSuccess('Parcel updated successfully');
            } else {
                $this->parcelRepo->store($request);
                return $this->responseWithSuccess('Parcel stored successfully');

            }

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function parcelDetail(Request $request, $id = null): \Illuminate\Http\JsonResponse
    {
        try {
            $user    = jwtUser();
            $parcel  = Parcel::with('merchant.user', 'events', 'branch')->find($id);

            if (!$parcel) {
                return $this->responseWithError('Parcel not found.');
            }

            if ($user->user_type == 'merchant' && $parcel->merchant->id != $user->merchant->id) {
                return $this->responseWithDanger(__('you_are_not_allowed'));
            }

            if ($user->user_type == 'merchant_staff' && !(
                ($parcel->merchant->id == $user->merchant_id && hasPermission('all_parcel')) ||
                ($parcel->merchant->id == $user->merchant_id && $parcel->user_id == $user->id)
            )) {
                return $this->responseWithDanger(__('access_denied'));
            }

            $parcel = Parcel::where('id', $id)->with('merchant.user', 'events', 'branch')->first();




                $merchant_name = '';
                $email = '';
                $pickup_date = $parcel->pickup_date ? date('M d, Y', strtotime($parcel->pickup_date)) : '';
                $delivery_date = $parcel->delivery_date ? date('M d, Y', strtotime($parcel->delivery_date)) : '';



                if ($user->user_type == 'merchant') {
                    $merchant_name = $user->first_name . ' ' . $user->last_name;
                    $email = $user->email;
                } elseif ($user->user_type == 'merchant_staff') {
                    $merchant_name = $user->first_name . ' ' . $user->last_name;
                    $email = $user->email;
                }

                $status = $parcel->status;
                $total_delivery_charge = $parcel->total_delivery_charge;

                $events = [];

                foreach ($parcel->events as $event) {
                    $eventData = [
                        'created_at' => date('M d, Y H:i:s', strtotime($event->created_at)),
                        'title' => __($event->title),
                        'cancel_note' => $event->cancel_note ?: null,
                    ];

                    if ($event->title == 'assign_pickup_man_event' || $event->title == 'parcel_re_schedule_pickup_event') {
                        $eventData['pickup_man'] = @$event->pickupPerson ? $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name : null;
                        $eventData['pickup_phone_number'] = @$event->pickupPerson ? $event->pickupPerson->phone_number : null;
                    }

                    if ($event->title == 'assign_delivery_man_event' || $event->title == 'parcel_re_schedule_delivery_event') {
                        if ($parcel->location == 'dhaka') {
                            $eventData['delivery_man'] = $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name;
                            $eventData['delivery_phone_number'] = $event->deliveryPerson->phone_number;
                        } else {
                            $eventData['delivery_man'] = null;
                            $eventData['delivery_phone_number'] = null;
                        }
                    }

                    $events[] = $eventData;

                }

                $data = [
                    'id'                     => (int)$parcel->id,
                    'company_name'           => $parcel->merchant->company,
                    'merchant_name'          => $merchant_name,
                    'pickup_number'          => $parcel->pickup_shop_phone_number,
                    'pickup_address'         => $parcel->pickup_address,
                    'email'                  => $email,
                    'pickup_date'            => $pickup_date,
                    'delivery_date'          => $delivery_date,
                    'parcel_type'            => $parcel->parcel_type,
                    'created_at'             => $parcel->created_at->format('d-m-Y H:i:s'),
                    'updated_at'             => $parcel->updated_at->format('d-m-Y H:i:s'),
                    'total_charge'           => $total_delivery_charge,
                    'customer_invoice_no'    => $parcel->customer_invoice_no,
                    'customer_name'          => $parcel->customer_name,
                    'customer_phone_number'  => $parcel->customer_phone_number,
                    'customer_address'       => $parcel->customer_address,
                    'location'               => $parcel->location,
                    'weight'                 => $parcel->weight,
                    'price'                  => $parcel->price,
                    'fragile'                => $parcel->fragile,
                    'note'                   => $parcel->note,
                    'packaging'              => $parcel->packaging,
                    'shop_id'                => $parcel->shop_id,
                    'selling_price'          => $parcel->selling_price,
                    'events'                 => $events,
                ];

            return $this->responseWithSuccess('parcel_details_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError('An error occurred while fetching parcel details.');
        }
    }


    public function import(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        try {
            $user       = jwtUser();
            $extension = request()->file('file')->getClientOriginalExtension();

            if ($extension != 'xlsx' && $extension != 'csv'):
                return back()->with('danger', __('file_type_not_supported'));
            endif;

            $file       = request()->file('file')->store('import');
            $import     = new ParcelsImport();
            $import->import($file);
            unlink(storage_path('app/'.$file));

            return $this->responseWithSuccess('imported_successfully');
        } catch (\Exception $e) {
            return $this->responseWithError('An error occurred while fetching parcel details.');
        }
    }

    public function parcelStatus(): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        try{
            $status = \Config::get('parcel.parcel_status');

            $data = [
                'status' => $status,
            ];

            return $this->responseWithSuccess('parcel_status_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function setting(): \Illuminate\Http\JsonResponse
    {
        try{
            $packaging_charge = settingHelper('package_and_charges');
            $default_currency = setting('default_currency_symbol');
            $default_unit   = setting('default_unit');

            $data = [
                'packaging_charge' => $packaging_charge,
                'default_currency' => $default_currency,
                'default_unit'   => $default_unit,
            ];

            return $this->responseWithSuccess('packaging_charge_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function parcelDelete(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        try {
            $user                           = jwtUser();
            $parcel = $this->parcelRepo->get($request->id);
            if ($user->user_type == 'merchant_staff') {
                $merchant = $user->merchant_id;
            }else{
                $merchant = $user->merchant->id;
            }

            if ($parcel->merchant->id == $merchant):

                if ($parcel->status == 'deleted'):
                    return $this->responseWithError(__('this_parcel_has_already_been_deleted'), [], 500);
                endif;

                if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup'):

                    if($this->parcelRepo->parcelDelete($request)):
                        return $this->responseWithSuccess('deleted_successfully');
                    else:
                        return $this->responseWithSuccess('something_went_wrong_please_try_again');
                    endif;
                else:
                    return $this->responseWithSuccess('this_parcel_can_not_be_deleted');
                endif;
            else:
                return $this->responseWithSuccess('you_are_not_allowed');
            endif;
        } catch (\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function export()
    {
        try {
            $user     = jwtUser();
            $filename = ($user->user_type == 'merchant' || $user->user_type == 'merchant_staff')
                        ? 'admin/excel/merchant-parcel-import-sample.xlsx'
                        : 'admin/excel/staff-parcel-import-sample.xlsx';

            if (file_exists(public_path($filename))) {

                $filepath = static_asset($filename);
                $data     = [
                    'url' => $filepath,
                ];
                return $this->responseWithSuccess('parcel_download_successfuly', [], $data);

            } else {
                return $this->responseWithError(__('file_not_found'), [], 500);
            }
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function parcelDownload(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            $query                          = Parcel::query();
            $user                           = jwtUser();

            if ($user->user_type == 'merchant_staff') {
                $query->where('merchant_id', $user->merchant_id);
                $userPermissions            = $user->permissions;
                if (is_array($userPermissions) && !in_array('all_parcel', $userPermissions)) {
                    $query->where('user_id', $user->id);
                }
            }

            if ($user->user_type == 'merchant') {
                $query->where('merchant_id', $user->merchant->id);
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
                $pickup_date   = date("Y-m-d", strtotime($request->pickup_date));
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
                $query->where('status', $request->status);
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
            $data           = $query->get();

            return $this->responseWithSuccess('download_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }

    }


    public function cancelParcel(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        try {
            $user     = jwtUser();
            $parcel   = $this->parcelRepo->get($request->id);
            
            if ($user->user_type == 'merchant_staff') {
                $merchant = $user->merchant_id;
            } else {
                $merchant = $user->merchant->id;
            }

            if ($parcel->merchant->id == $merchant) {
                if ($parcel->status == 'cancel') {
                    return $this->responseWithError(__('this_parcel_has_already_been_cancelled'), [], 500);
                }

                if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup') {
                    if ($this->parcelRepo->parcelCancel($request)) {
                        return $this->responseWithSuccess('cancelled_successfully');
                    } else {
                        return $this->responseWithError('something_went_wrong_please_try_again');
                    }
                } else {
                    return $this->responseWithError('this_parcel_can_not_be_cancelled');
                }
            } else {
                return $this->responseWithError('you_are_not_allowed');
            }
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function PercelTracking(Request $request){
         if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
       try {
            $user    = jwtUser();
            $parcel  = Parcel::with('merchant.user', 'events', 'branch')->where('parcel_no',$request->tracking_number)->first();

            if (!$parcel) {
                return $this->responseWithError('Parcel not found.');
            }

            if ($user->user_type == 'merchant' && $parcel->merchant->id != $user->merchant->id) {
                return $this->responseWithDanger(__('you_are_not_allowed'));
            }

            if ($user->user_type == 'merchant_staff' && !(
                ($parcel->merchant->id == $user->merchant_id && hasPermission('all_parcel')) ||
                ($parcel->merchant->id == $user->merchant_id && $parcel->user_id == $user->id)
            )) {
                return $this->responseWithDanger(__('access_denied'));
            }

            $parcel = Parcel::where('parcel_no', $request->tracking_number)->with('merchant.user', 'events', 'branch')->first();




                $merchant_name = '';
                $email = '';
                $pickup_date = $parcel->pickup_date ? date('M d, Y', strtotime($parcel->pickup_date)) : '';
                $delivery_date = $parcel->delivery_date ? date('M d, Y', strtotime($parcel->delivery_date)) : '';



                if ($user->user_type == 'merchant') {
                    $merchant_name = $user->first_name . ' ' . $user->last_name;
                    $email = $user->email;
                } elseif ($user->user_type == 'merchant_staff') {
                    $merchant_name = $user->first_name . ' ' . $user->last_name;
                    $email = $user->email;
                }

                $status = $parcel->status;
                $total_delivery_charge = $parcel->total_delivery_charge;

                $events = [];

                foreach ($parcel->events as $event) {
                    $eventData = [
                        'created_at' => date('M d, Y H:i:s', strtotime($event->created_at)),
                        'title' => __($event->title),
                        'cancel_note' => $event->cancel_note ?: null,
                    ];

                    if ($event->title == 'assign_pickup_man_event' || $event->title == 'parcel_re_schedule_pickup_event') {
                        $eventData['pickup_man'] = @$event->pickupPerson ? $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name : null;
                        $eventData['pickup_phone_number'] = @$event->pickupPerson ? $event->pickupPerson->phone_number : null;
                    }

                    if ($event->title == 'assign_delivery_man_event' || $event->title == 'parcel_re_schedule_delivery_event') {
                        if ($parcel->location == 'dhaka') {
                            $eventData['delivery_man'] = $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name;
                            $eventData['delivery_phone_number'] = $event->deliveryPerson->phone_number;
                        } else {
                            $eventData['delivery_man'] = null;
                            $eventData['delivery_phone_number'] = null;
                        }
                    }

                    $events[] = $eventData;

                }

                $data = [
                    'id'                     => (int)$parcel->id,
                    'company_name'           => $parcel->merchant->company,
                    'merchant_name'          => $merchant_name,
                    'pickup_number'          => $parcel->pickup_shop_phone_number,
                    'pickup_address'         => $parcel->pickup_address,
                    'email'                  => $email,
                    'pickup_date'            => $pickup_date,
                    'delivery_date'          => $delivery_date,
                    'parcel_type'            => $parcel->parcel_type,
                    'created_at'             => $parcel->created_at->format('d-m-Y H:i:s'),
                    'updated_at'             => $parcel->updated_at->format('d-m-Y H:i:s'),
                    'total_charge'           => $total_delivery_charge,
                    'customer_invoice_no'    => $parcel->customer_invoice_no,
                    'customer_name'          => $parcel->customer_name,
                    'customer_phone_number'  => $parcel->customer_phone_number,
                    'customer_address'       => $parcel->customer_address,
                    'location'               => $parcel->location,
                    'weight'                 => $parcel->weight,
                    'price'                  => $parcel->price,
                    'fragile'                => $parcel->fragile,
                    'note'                   => $parcel->note,
                    'packaging'              => $parcel->packaging,
                    'shop_id'                => $parcel->shop_id,
                    'selling_price'          => $parcel->selling_price,
                    'events'                 => $events,
                ];

            return $this->responseWithSuccess('parcel_details_retrieved_successfully', [], $data);        
        }
        catch(\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

}
