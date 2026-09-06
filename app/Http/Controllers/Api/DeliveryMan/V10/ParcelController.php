<?php

namespace App\Http\Controllers\Api\DeliveryMan\V10;

use App\Http\Controllers\Controller;
use App\Models\CustomerParcelSmsTemplates;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\PercelOtpLog;
use App\Models\SmsTemplate;
use App\Repositories\Interfaces\AccountInterface;
use App\Repositories\Interfaces\ParcelInterface;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\SmsSenderTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use DB;

class ParcelController extends Controller
{
    use ApiReturnFormatTrait, SmsSenderTrait;

    protected $parcels;
    protected $accounts;

    public function __construct(ParcelInterface $parcels, AccountInterface $accounts)
    {
        $this->parcels          = $parcels;
        $this->accounts          = $accounts;
    }

    public function myPickup(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            $my_pickup = Parcel::with('merchant','pickupMan','deliveryMan')->where('pickup_man_id', $user->deliveryMan->id)->whereIn('status', ['pickup-assigned','re-schedule-pickup'])->orderByDesc('id')->latest()->skip($offset)->take($limit)->get();

            $log = $this->parcelListReturnFormat($my_pickup);

            $data = [
                'my_pickup' => $log,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }
    }

    public function pickupPending(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            $pending   = Parcel::with('merchant','pickupMan','deliveryMan')->where('status', 'pending')->orderByDesc('id')->latest()->skip($offset)->take($limit)->get();
            $log   = $this->parcelListReturnFormat($pending);
            $data = [
                'pending_pickup' => $log,
            ];
            return $this->responseWithSuccess(__('successfully_found'),'', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }
    }

    public function pickupCompleted(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            $completed = Parcel::with('merchant','pickupMan','deliveryMan')->where('pickup_man_id', $user->deliveryMan->id)->where('status', 'received-by-pickup-man')->orderByDesc('id')->latest()->skip($offset)->take($limit)->get();

            $log = $this->parcelListReturnFormat($completed);

            $data = [
                'completed_pickup' => $log,
            ];

            return $this->responseWithSuccess(__('successfully_found'), ' ' ,$data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }
    }


    public function shopWisePendingPickup(Request $request)
    {
        try {
            // Authenticate user with JWT
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $page   = $request->page ?? 1;
            $limit  = config('parcel.api_paginate');
            $offset = ($page * $limit) - $limit;

            $pendingPickups = Parcel::with(['pickupMan', 'deliveryMan'])
                ->select(
                    'merchants.company as merchant_name',
                    'merchants.id as merchant_id',
                    'shops.id as shop_id',
                    'shops.shop_name as shop_name',
                    'shops.shop_phone_number as shop_phone_number',
                    'shops.address as shop_address',
                    \DB::raw('GROUP_CONCAT(parcels.id) as parcel_ids'),
                    \DB::raw('COUNT(parcels.id) as total_parcels'),
                    \DB::raw('SUM(parcels.price) as total_amount')
                )
                ->join('shops', 'parcels.shop_id', '=', 'shops.id')
                ->join('merchants', 'shops.merchant_id', '=', 'merchants.id')
                ->where('pickup_man_id', $user->deliveryMan->id)
                ->whereIn('parcels.status', ['pending', 'pickup-assigned', 're-schedule-pickup'])
                ->groupBy('shops.id', 'merchants.id')
                ->skip($offset)
                ->take($limit)
                ->get();

            // Structure response data
            $data = [
                'pending_pickup' => $pendingPickups,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data);
        } catch (\Exception $e) {
            // Return detailed error message if any exception occurs
            return $this->responseWithError($e->getMessage(), '', []);
        }
    }


    public function shopWisePickedup(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        \DB::beginTransaction();
        try {

            if (!is_string($request->parcel_ids)) {
                return $this->responseWithError(__('invalid_parcel_ids_format'), '', [], 422);
            }

            $request->parcel_ids = str_replace("'", '"', $request->parcel_ids); // Change single quotes to double quotes
            $request->parcel_ids = json_decode($request->parcel_ids);

            if (!is_array($request->parcel_ids) || empty($request->parcel_ids)) {
                return $this->responseWithError(__('invalid_parcel_ids_format'), '', [], 422);
            }

            $request->parcel_ids = array_map('trim', $request->parcel_ids);

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->responseWithError(__('unauthorized_user'), '', [], 404);
            }

            foreach ($request->parcel_ids as $parcel_id) {
                $parcel_id = (int) $parcel_id;

                $parcel = Parcel::where('id', $parcel_id)->first();

                if (!$parcel) {
                    return $this->responseWithError(__('parcel_not_found'), '', [], 404);
                }
                if (!in_array($parcel->status, ['pickup-assigned'])) {
                    return $this->responseWithError(__('this_parcel_can_not_get_received'), '', [], 422);
                }

                $parcel->date = now()->format('Y-m-d');
                $parcel->status = 'received-by-pickup-man';
                $parcel->save();

                $this->parcelEvent($parcel->id, 'parcel_received_by_pickup_man_event', $request->note, $user->id);
            }

            \DB::commit();
            return $this->responseWithSuccess(__('successfully_received'), '', [], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->responseWithError($e->getMessage(), '', [], 500);
        }
    }











    public function processingDelivery(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $page = $request->page ?? 1;
            $limit = \Config::get('parcel.api_paginate');
            $offset = ($page - 1) * $limit;

            $my_delivery = Parcel::with('merchant', 'pickupMan.user', 'deliveryMan.user')
                                ->where('delivery_man_id', $user->deliveryMan->id)
                                ->whereIn('status', ['delivery-assigned', 're-schedule-delivery'])
                                ->orderByDesc('id')
                                ->skip($offset)
                                ->take($limit)
                                ->get();

            $log = $this->parcelListReturnFormat($my_delivery);

            $data = [
                'processing_delivery' => $log,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }
    }


    public function deliveryCancelled(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $page = $request->page ?? 1;
            $offset = ($page * \Config::get('parcel.api_paginate')) - \Config::get('parcel.api_paginate');
            $limit = \Config::get('parcel.api_paginate');

            $cancel = Parcel::with('merchant', 'pickupMan.user', 'deliveryMan.user')
                ->where('delivery_man_id', $user->deliveryMan->id)
                ->where('status', 'cancel')
                ->orderByDesc('id')
                ->skip($offset)
                ->take($limit)
                ->get();


            $log = $this->parcelListReturnFormat($cancel);

            $data = [
                'cancelled_delivery' => $log,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'));
        }
    }



    public function myReScheduledDelivery(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }
            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            $my_delivery    = Parcel::with('merchant','pickupMan.user','deliveryMan.user')
                            ->where('delivery_man_id', $user->deliveryMan->id)
                            ->where('status', 're-schedule-delivery')
                            ->orderByDesc('id')
                            ->latest()
                            ->skip($offset)
                            ->take($limit)
                            ->get();

            $data = $this->parcelListReturnFormat($my_delivery, true);

            return $this->responseWithSuccess(__('successfully_found'),  '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }

    }

    public function deliveryPending(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            $pending        = Parcel::with('merchant','pickupMan.user','deliveryMan.user')->where('status', 'received')->orderByDesc('id')->latest()->skip($offset)->take($limit)->get();

            $log = $this->parcelListReturnFormat($pending);

            $data = [
                'processing_delivery' => $log,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }
    }

    public function deliveryCompleted(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page           = $request->page ?? 1;
            $offset         = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit          = \Config::get('parcel.api_paginate');
            $completed      = Parcel::with('merchant','pickupMan.user','deliveryMan.user')->where('delivery_man_id', $user->deliveryMan->id)->whereIn('status', ['delivered','delivered-and-verified'])->orderByDesc('id')->latest()->skip($offset)->take($limit)->get();
            $log            = $this->parcelListReturnFormat($completed);

            $data = [
                'completed_delivery' => $log,
            ];
            return $this->responseWithSuccess(__('successfully_found'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }

    }

    public function cancelReason(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '', 401);
            }

            $log = \Config::get('parcel.cancel_predefined_reasons');

            $data = [
                'cancelled_delivery' => $log,
            ];

            return $this->responseWithSuccess(__('successfully_found'),' ', $data);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->responseWithError(__('token_error'), '', 401);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', []);
        }
    }



    public function parcelListReturnFormat($parcels, $reschedule_status = false)
    {
        foreach ($parcels as $parcel):
            $parcel['re_scheduled_status'] = $parcel->status == 're-schedule-delivery' ? 1 : 0;
            $parcel['parcel_no'] = $parcel->parcel_no;
            $parcel['parcel_type'] = __($parcel->parcel_type);
            $parcel['weight'] = $parcel->weight.' '.__('kg');
            $parcel['charge'] = $parcel->charge.' ' . setting('default_currency');
            $parcel['cod_charge'] = $parcel->cod_charge.' ' . setting('default_currency');
            $parcel['vat'] = $parcel->vat.' '.__('tk');
            $parcel['location'] = __($parcel->location);
            $parcel['total_delivery_charge'] = __($parcel->total_delivery_charge).' ' . setting('default_currency');
            $parcel['payable'] = __($parcel->payable).' ' . setting('default_currency');
            $parcel['price'] = __($parcel->price).' ' . setting('default_currency');
            $parcel['selling_price'] = __($parcel->selling_price).' ' . setting('default_currency');
            $parcel['merchant_company'] = $parcel->merchant->company;
            $parcel['shop_name'] = @$parcel->shop->shop_name;
            $parcel['cancel_note'] = @$parcel->event->cancel_note;
            $parcel['pickup_person'] = @$parcel->pickupMan->user['first_name'].' '.@$parcel->pickupMan->user['last_name'];
            $parcel['delivery_person'] = @$parcel->deliveryMan->user['first_name'].' '.@$parcel->deliveryMan->user['last_name'];
            $parcel['status'] = __($parcel->status);
            $parcel['status_before_cancel'] = __($parcel->status_before_cancel);
            $parcel['created'] = date('M d, Y g:i A', strtotime($parcel->created_at));
            $parcel['updated'] = date('M d, Y g:i A', strtotime($parcel->created_at));
            unset($parcel->merchant_id);
            unset($parcel->pickup_man_id);
            unset($parcel->delivery_man_id);
            unset($parcel->merchant);
            unset($parcel->pickupMan);
            unset($parcel->deliveryMan);
            unset($parcel->created_at);
            unset($parcel->updated_at);
            unset($parcel->shop->shop_phone_number);
            unset($parcel->shop->contact_number);
            unset($parcel->shop->pickup_branch_id);
            unset($parcel->shop->address);
            unset($parcel->shop->merchant_id);
            unset($parcel->shop->default);
            unset($parcel->shop->status);
            unset($parcel->shop->created_by);
            unset($parcel->shop->updated_by);
            unset($parcel->shop->created_at);
            unset($parcel->shop->updated_at);


        endforeach;

        return $parcels;
    }

    public function paginationFormat($parcels)
    {
        if (isset($parcels['links'])) {
            unset($parcels['links']);
        }
        if (isset($parcels['meta'], $parcels['meta']['links'])) {
            unset($parcels['meta']['links']);
        }

        return $parcels;
    }

    public function parcelDeliveryConfirm(Request $request)
    {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'id'    => 'required|max:50',
                'otp'   => 'required|max:50',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $parcel = Parcel::find($request->id);

            if ($parcel->status == 'delivered-and-verified'):
                return $this->responseWithError(__('this_parcel_has_already_been_delivered_and_verified'), '', [], 422);
            endif;
            if ($parcel->status != 'delivered' || $parcel->status != 'partially-delivered'):
                return $this->responseWithError(__('this_parcel_yet_not_delivered'), '', [], 422);
            endif;

            if (isset($parcel)):
                $maxAttempts = 3;

                if (($parcel->otp_attempts ?? 0) >= $maxAttempts):
                    try {
                        PercelOtpLog::create([
                            'parcel_id'       => $parcel->id,
                            'delivery_man_id' => $user->deliveryMan->id ?? null,
                            'action'          => 'locked',
                            'otp_code'        => $parcel->otp,
                            'submitted_otp'   => $request->otp,
                            'attempt_number'  => $parcel->otp_attempts,
                            'source'          => 'rider_app',
                            'status_message'  => 'Maximum wrong attempts reached. Verification locked.',
                        ]);
                    } catch (\Exception $logEx) {}

                    return $this->responseWithError(__('max_wrong_otp_attempts_reached_please_resend'), '', [], 422);
                endif;

                if (!empty($parcel->otp_expired_at) && Carbon::now()->gt(Carbon::parse($parcel->otp_expired_at))):
                    try {
                        PercelOtpLog::create([
                            'parcel_id'       => $parcel->id,
                            'delivery_man_id' => $user->deliveryMan->id ?? null,
                            'action'          => 'expired',
                            'otp_code'        => $parcel->otp,
                            'submitted_otp'   => $request->otp,
                            'attempt_number'  => $parcel->otp_attempts,
                            'source'          => 'rider_app',
                            'status_message'  => 'Submitted OTP has expired',
                        ]);
                    } catch (\Exception $logEx) {}

                    return $this->responseWithError(__('otp_has_been_expired_please_resend'), '', [], 422);
                endif;

                if ($parcel->otp == $request->otp):
                    $parcel->status         = 'delivered-and-verified';
                    $parcel->date           = date('Y-m-d');
                    $parcel->otp_attempts   = 0;
                    $parcel->save();

                    $parcel_event                      = new ParcelEvent();
                    $parcel_event->parcel_id           = $parcel->id;
                    $parcel_event->delivery_man_id     = $parcel->delivery_man_id;
                    $parcel_event->pickup_man_id       = $parcel->pickup_man_id;
                    $parcel_event->user_id             = $user->id;
                    $parcel_event->title               = 'parcel_delivered_and_verified_event';
                    $parcel_event->cancel_note         = $request->note;
                    $parcel_event->save();

                    try {
                        PercelOtpLog::create([
                            'parcel_id'       => $parcel->id,
                            'delivery_man_id' => $user->deliveryMan->id ?? null,
                            'action'          => 'success',
                            'otp_code'        => $parcel->otp,
                            'submitted_otp'   => $request->otp,
                            'attempt_number'  => 1,
                            'source'          => 'rider_app',
                            'status_message'  => 'Delivery OTP verified successfully',
                        ]);
                    } catch (\Exception $logEx) {}

                    DB::commit();

                    return $this->responseWithSuccess(__('delivery_successfully_verified'),'', [] ,200);
                else:
                    $parcel->otp_attempts = ($parcel->otp_attempts ?? 0) + 1;
                    $parcel->save();

                    $isLocked = ($parcel->otp_attempts >= $maxAttempts);
                    try {
                        PercelOtpLog::create([
                            'parcel_id'       => $parcel->id,
                            'delivery_man_id' => $user->deliveryMan->id ?? null,
                            'action'          => $isLocked ? 'locked' : 'wrong_attempt',
                            'otp_code'        => $parcel->otp,
                            'submitted_otp'   => $request->otp,
                            'attempt_number'  => $parcel->otp_attempts,
                            'source'          => 'rider_app',
                            'status_message'  => $isLocked ? 'Maximum 3 wrong attempts reached. OTP locked.' : 'Incorrect OTP code entered',
                        ]);
                    } catch (\Exception $logEx) {}

                    DB::commit();

                    $remaining = $maxAttempts - $parcel->otp_attempts;
                    if ($remaining > 0) {
                        $errMsg = __('wrong_otp_code_remaining_attempts', ['remaining' => $remaining]);
                    } else {
                        $errMsg = __('max_wrong_otp_attempts_reached_please_resend');
                    }

                    return $this->responseWithError($errMsg, '', [] , 422);
                endif;
            else:
                return $this->responseWithError(__('parcel_not_found'), '', [] , 404);
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong'), '', [], 500);
        }
    }

    public function resendDeliveryOtp(Request $request)
    {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $parcel = Parcel::find($request->id);
            if (!$parcel) {
                return $this->responseWithError(__('parcel_not_found'), '', [], 404);
            }

            if ($parcel->status != 'delivered') {
                return $this->responseWithError(__('parcel_not_delivered_yet'), '', [], 422);
            }

            $parcel->otp = rand(1000, 9999);
            $parcel->otp_expired_at = Carbon::now()->addMinutes(5);
            $parcel->otp_attempts = 0;
            $parcel->save();

            try {
                PercelOtpLog::create([
                    'parcel_id'       => $parcel->id,
                    'delivery_man_id' => $user->deliveryMan->id ?? null,
                    'action'          => 'resend',
                    'otp_code'        => $parcel->otp,
                    'attempt_number'  => 0,
                    'source'          => 'rider_app',
                    'status_message'  => 'New OTP generated and resent to customer',
                ]);
            } catch (\Exception $logEx) {}

            try {
                $sms_template = CustomerParcelSmsTemplates::where('subject','delivery_confirm_otp')->first();
                if ($sms_template && $sms_template->sms_to_customer) {
                    $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{otp}', $parcel->otp, $sms_body);
                    $sms_body = str_replace('{our_company_name}', setting('company_name') ? setting('company_name') : __('app_name'), $sms_body);
                    $this->send($sms_body, $parcel->customer_phone_number, '', '', $sms_template->masking);
                }
            } catch (\Exception $smsEx) {
            }

            DB::commit();
            return $this->responseWithSuccess(__('otp_resend_successfully'), $parcel, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong'), '', [], 500);
        }
    }

    public function reshedulePickup(Request $request)
    {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'id'    => 'required|max:50',
                'date'     => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }
            $parcel                       = Parcel::find($request->id);
            $parcel->status               = 're-schedule-pickup';
            $parcel->date                 = date('Y-m-d');
            $parcel->pickup_date          = date('Y-m-d', strtotime($request->date));
            $parcel->pickup_time          = date('h:i:s', strtotime($request->time));
            $parcel->pickup_man_id        = $user->deliveryMan->id;
            $parcel->pickup_fee           = DeliveryMan::find($parcel->pickup_man_id)->pick_up_fee;
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_re_schedule_pickup_event',$request->note , $user->id);

            DB::commit();
            return $this->responseWithSuccess(__('successfully_re_scheduled'));

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }

    }

    public function resheduleDelivery(Request $request)
    {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'id'    => 'required|max:50',
                'date'     => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $parcel                       = $this->parcels->get($request->id);
            if ($parcel->status == 're-schedule-delivery' || $parcel->status == 'delivery-assigned'):

                $parcel->status               = 're-schedule-delivery';
                $parcel->date                 = date('Y-m-d');
                $parcel->delivery_date        = date('Y-m-d', strtotime($request->date));
                $parcel->delivery_time        = date('h:i:s', strtotime($request->time));
                $parcel->delivery_man_id      = $user->deliveryMan->id;
                $parcel->delivery_fee         = DeliveryMan::find($parcel->delivery_man_id)->delivery_fee;
                $parcel->save();

                $this->parcelEvent($parcel->id, 'parcel_re_schedule_delivery_event',  $request->note, $user->id);

                DB::commit();
                return $this->responseWithSuccess(__('successfully_re_scheduled'));
            else:
                return $this->responseWithError(__('you_cant_re_schedule_this_parcel_anymore'));
            endif;

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', []);
        }

    }

    public function cancel(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(),[
                'id'    => 'required|max:50',
                'cancel_note'  => 'required',
            ]);

            if ($validator->fails()){
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()){
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $request['delivery_man'] = $user->deliveryMan->id;
            $parcel                         = Parcel::find($request->id);

            if ($parcel->status == 'cancel'):
                return $this->responseWithError(__('this_parcel_has_already_been_cancelled'));
            endif;

            if ($parcel->status == 'received' || $parcel->status == 'delivered' || $parcel->status == 'partially-delivered' || $parcel->status == 'delivered-and-verified' || $parcel->status == 'returned-to-merchant'):
                return $this->responseWithError(__('this_parcel_can_not_be_cancelled'));
            endif;

            $this->accounts->incomeExpenseManageCancel($request->id, 'cancel');


            $parcel->status_before_cancel   = $parcel->status;
            $parcel->status                 = 'cancel';
            $parcel->date                   = date('Y-m-d');
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_cancel_event', $request->cancel_note, $user->id);

            DB::commit();
            return $this->responseWithSuccess(__('successfully_cancelled'), '', [], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function delivery(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(),[
                'id'    => 'required|max:50',
            ]);

            if ($validator->fails()){
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()){
                return $this->responseWithError(__('unauthorized_user'), '', [], 404);
            }

            $parcel                 = Parcel::find($request->id);


            if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified' || $parcel->status == 'partially-delivered'):
                return $this->responseWithError(__('this_parcel_has_already_confirmed_as_delivered'),  '', [], 422);
            endif;

            $parcel->date           = date('Y-m-d');
            $parcel->status         = 'delivered';

            $this->accounts->incomeExpenseManage($request->id, 'delivered');

            $parcel->otp = rand(1000, 9999);
            $parcel->otp_expired_at = Carbon::now()->addMinutes(5);
            $parcel->otp_attempts = 0;
            if (empty($parcel->delivery_man_id) && isset($user->deliveryMan)) {
                $parcel->delivery_man_id = $user->deliveryMan->id;
            }
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_delivered_event', $request->note, $user->id);

            try {
                PercelOtpLog::create([
                    'parcel_id'       => $parcel->id,
                    'delivery_man_id' => $user->deliveryMan->id ?? null,
                    'action'          => 'generated',
                    'otp_code'        => $parcel->otp,
                    'attempt_number'  => 0,
                    'source'          => 'rider_app',
                    'status_message'  => 'Delivery OTP generated and sent to customer',
                ]);
            } catch (\Exception $logEx) {}

            try {
                $sms_template = CustomerParcelSmsTemplates::where('subject','delivery_confirm_otp')->first();
                if ($sms_template && $sms_template->sms_to_customer) {
                    $sms_body = str_replace('{merchant_name}', @$parcel->merchant->company, $sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{otp}', $parcel->otp, $sms_body);
                    $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
                    $this->test('delivery_confirm_otp', $parcel->customer_phone_number, $sms_body, $sms_template->masking);
                }
            } catch (\Exception $smsEx) {
            }

            DB::commit();
            return $this->responseWithSuccess(__('successfully_delivered'), '', [], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function parcelDetails($id)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()){
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }
            $parcel = $this->parcels->get($id);

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
                $merchant['total_charge'] = __($parcel->total_delivery_charge) . ' ' . __('tk');
                $merchant['pickup_person'] = @$parcel->pickupMan->user['first_name'] . ' ' . @$parcel->pickupMan->user['last_name'];
                $merchant['delivery_person'] = @$parcel->deliveryMan->user['first_name'] . ' ' . @$parcel->deliveryMan->user['last_name'];
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
                $customer['customer_address'] = $parcel->customer_address;
                $customer['location'] = __($parcel->location);
                $customer['note'] = __($parcel->note);
                $customer['weight'] = $parcel->weight . ' ' . __('kg');
                $customer['total_cod'] = $parcel->price . ' ' . __('tk');

                $events = $parcel->events;

                foreach ($events as $event):
                    $event['title'] = __($event->title);
                    $event['date'] = date('d M Y', strtotime($event->created_at));
                    $event['time'] = date('g:i a', strtotime($event->created_at));
                    $event['processed_by'] = $event->user['first_name'] . ' ' . $event->user['last_name'];
                    $event['pickup_man'] = @$event->pickupPerson->user->first_name . ' ' . @$event->pickupPerson->user->last_name;
                    $event['pickup_man_phone'] = @$event->pickupPerson->phone_number ?? '';
                    $event['delivery_man'] = @$event->deliveryPerson->user->first_name . ' ' . @$event->deliveryPerson->user->last_name;
                    $event['delivery_man_phone'] = @$event->deliveryPerson->phone_number ?? '';

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
                endforeach;

                $data['merchant'] = $merchant;
                $data['customer'] = $customer;
                $data['events'] = $events;

                return $this->responseWithSuccess(__('successfully_delivered'), '', $data, 200);
            else:
                return $this->responseWithError(__('parcel_not_found'), '', [], 404);
            endif;

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '',[], 500);
        }
    }

    public function parcelEvent($parcel_id, $title, $cancel_note = '', $user_id)
    {
        $parcel = $this->parcels->get($parcel_id);
        $parcel_event                      = new ParcelEvent();

        $parcel_event->parcel_id           = $parcel_id;
        $parcel_event->delivery_man_id     = $parcel->delivery_man_id;
        $parcel_event->pickup_man_id       = $parcel->pickup_man_id;
        $parcel_event->user_id             = $user_id;
        $parcel_event->title               = $title;
        $parcel_event->cancel_note         = $cancel_note;

        $parcel_event->save();

        $delivery_person = DeliveryMan::where('id',$parcel->delivery_man_id)->first();
        $pickup_person   = DeliveryMan::where('id',$parcel->pickup_man_id)->first();

        // merchant sms start
        $sms_template = SmsTemplate::where('subject',$title)->first();

        $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $sms_template->content);
        $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
        $sms_body = str_replace('{pickup_date_time}',  date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
        $sms_body = str_replace('{re_pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
        $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
        $sms_body = str_replace('{re_delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
        $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
        $sms_body = str_replace('{return_date_time}', date('M d, Y h:i a'), $sms_body);
        $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
        $sms_body = str_replace('{pickup_man_name}', @$pickup_person->user->first_name, $sms_body);
        $sms_body = str_replace('{pickup_man_phone}', @$pickup_person->phone_number, $sms_body);
        $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
        $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
        $sms_body = str_replace('{cancel_note}', @$parcel->cancelnote->cancel_note, $sms_body);
        $sms_body = str_replace('{price}', @$parcel->price, $sms_body);
        if($sms_template->sms_to_merchant):
            $this->test($title, $parcel->merchant->phone_number, $sms_body, $sms_template->masking);
        endif;
        //merchant sms end

        //customer sms start
        $customer_sms_template = CustomerParcelSmsTemplates::where('subject',$title)->first();
        $sms_body = str_replace('{merchant_name}', $parcel->merchant->company, $customer_sms_template->content);
        $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
        $sms_body = str_replace('{pickup_date_time}',  date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
        $sms_body = str_replace('{re_pickup_date_time}', date('M d, Y', strtotime($parcel->pickup_date)), $sms_body);
        $sms_body = str_replace('{delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
        $sms_body = str_replace('{re_delivery_date_time}', date('M d, Y', strtotime($parcel->delivery_date)), $sms_body);
        $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
        $sms_body = str_replace('{return_date_time}', date('M d, Y h:i a'), $sms_body);
        $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
        $sms_body = str_replace('{pickup_man_name}', @$pickup_person->user->first_name, $sms_body);
        $sms_body = str_replace('{pickup_man_phone}', @$pickup_person->phone_number, $sms_body);
        $sms_body = str_replace('{delivery_man_name}', @$delivery_person->user->first_name, $sms_body);
        $sms_body = str_replace('{delivery_man_phone}', @$delivery_person->phone_number, $sms_body);
        $sms_body = str_replace('{cancel_note}', @$parcel->cancelnote->cancel_note, $sms_body);
        $sms_body = str_replace('{price}', @$parcel->price, $sms_body);

        if($customer_sms_template->sms_to_customer):
            $this->test($title, $parcel->customer_phone_number, $sms_body, $customer_sms_template->masking);
        endif;
        //customer sms end

        return true;
    }

    public function track($id)
    {
        try{
            $parcel = Parcel::where('parcel_no', $id)->latest()->first();

            if (!blank($parcel)):
                $data['parcel_no'] = $parcel->parcel_no;
                $data['status'] = __($parcel->status);

                $merchant['merchan_name']       = $parcel->merchant->user->first_name.' '.$parcel->merchant->user->last_name;
                $merchant['merchant_company']   = $parcel->merchant->company;
                $merchant['phone_number']       = $parcel->merchant->phone_number;
                $merchant['address']            = $parcel->merchant->address;
                $merchant['email']              = $parcel->merchant->user->email;
                $merchant['created']            = date('M d, Y g:i a', strtotime($parcel->created_at));
                $merchant['parcel_type']        = __($parcel->parcel_type);
                $merchant['total_charge']       = __($parcel->total_delivery_charge).' '.__('tk');
                $merchant['pickup_person']      = @$parcel->pickupMan->user['first_name'].' '.@$parcel->pickupMan->user['last_name'];
                $merchant['delivery_person']    = @$parcel->deliveryMan->user['first_name'].' '.@$parcel->deliveryMan->user['last_name'];
                $merchant['return_delivery_person']    = @$parcel->returnDeliveryMan->user['first_name'].' '.@$parcel->returnDeliveryMan->user['last_name'];
                if($parcel->status != 'pending' && $parcel->status
                    != 'pickup-assigned' && $parcel->status != 're-schedule-pickup'
                    && $parcel->status != 'received-by-pickup-man'):
                    if (!blank($parcel->hub)):
                        $merchant['hub']                = @$parcel->hub->name.' ('.@$parcel->hub->address.')';
                    else:
                        $merchant['hub']                = '';
                    endif;
                endif;
                if($parcel->status == 'transferred-to-hub') :
                    $merchant['transferring_to_hub']    = @$parcel->transferToHub->name.' ('.@$parcel->transferToHub->address.')';
                endif;
                $merchant['pickup']             = date('M d, Y', strtotime($parcel->pickup_date));
                $merchant['delivery']           = date('M d, Y', strtotime($parcel->delivery_date));
                $merchant['pickup']             = date('M d, Y', strtotime($parcel->pickup_date));

                if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified'):
                    $merchant['delivered_at']       = date('M d, Y g:i A', strtotime($parcel->updated_at));
                endif;

                $customer['id']                 = $parcel->parcel_no;
                $customer['invno']              = $parcel->customer_invoice_no;
                $customer['customer_name']      = $parcel->customer_name;
                $customer['customer_mobile_no'] = $parcel->customer_phone_number;
                $customer['customer_mobile_no'] = $parcel->customer_address;
                $customer['location']           = __($parcel->location);
                $customer['note']               = __($parcel->note);
                $customer['weight']             = $parcel->weight.' '.__('kg');
                $customer['total_cod']          = $parcel->price.' '.__('tk');

                $events = $parcel->events;

                foreach ($events as $event):
                    $event['event']             = $event->title;
                    $event['title']             = __($event->title);
                    $event['date']              = date('d M Y', strtotime($event->created_at));
                    $event['time']              = date('g:i a', strtotime($event->created_at));
                    $event['processed_by']      = $event->user['first_name'].' '.$event->user['last_name'];
                    $event['pickup_man']        = @$event->pickupPerson->user->first_name.' '.@$event->pickupPerson->user->last_name;
                    $event['pickup_man_phone']  = @$event->pickupPerson->phone_number ?? '';
                    $event['delivery_man']      = @$event->deliveryPerson->user->first_name.' '.@$event->deliveryPerson->user->last_name;
                    $event['delivery_man_phone'] = @$event->deliveryPerson->phone_number ?? '';
                    $event['return_delivery_man']      = @$event->returnPerson->user->first_name.' '.@$event->returnPerson->user->last_name;
                    $event['return_delivery_man_phone'] = @$event->returnPerson->phone_number ?? '';
                    $hub = '';
                    if(!blank($event->hub)) :
                        $hub    = @$event->hub->name.' ('.@$event->hub->address.')';
                    endif;
                    $event['note'] = $event->cancel_note !=''? $event->cancel_note : '';

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
                    unset($event->hub);

                    $event['hub'] = $hub;
                endforeach;

                $data['merchant']   = $merchant;
                $data['customer']   = $customer;
                $data['events']     = $events;

                return $this->responseWithSuccess(__('successfully_delivered'), '', $data, 200);
            else:
                return $this->responseWithError(__('parcel_not_found'), '', [], 404);
            endif;

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function myPickupMerchants(Request $request)
    {
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            $my_pickup = Parcel::with('merchant.user')->groupBy('merchant_id')->where('pickup_man_id', $user->deliveryMan->id)->whereIn('status', ['pickup-assigned','re-schedule-pickup'])->orderByDesc('id')->latest()->skip($offset)->take($limit)->get(['merchant_id','pickup_shop_phone_number','pickup_address']);

            $data = $this->merchantReturnFormat($my_pickup);

            return $this->responseWithSuccess(__('successfully_found'),'', $data ,200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong'), '', [], 500);
        }
    }

    public function merchantReturnFormat($parcels)
    {
        foreach ($parcels as $parcel):
            $parcel['merchant_name'] = $parcel->merchant->user->first_name.' '.$parcel->merchant->user->last_name;
            $parcel['company'] = $parcel->merchant->company;
            $parcel['pickup_phone_number'] = $parcel->pickup_shop_phone_number;
            $address = $parcel->pickup_address;
            unset($parcel->pickup_address);
            $parcel['pickup_address'] = $address;
            unset($parcel->merchant_id);
            unset($parcel->pickup_shop_phone_number);
            unset($parcel->merchant);
        endforeach;

        return $parcels;
    }

    public function pickupReceived(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(),[
                'id'    => 'required|max:50',
            ]);

            if ($validator->fails()){
                return $this->responseWithError('required_field_missing', $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()){
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $parcel          = Parcel::find($request->id);

            if ($parcel->status != 'pickup-assigned' && $parcel->status != 're-schedule-pickup'):
                return $this->responseWithError(__('this_parcel_can_not_get_received'), '', [], 422);
            endif;

            $parcel->date    = date('Y-m-d');
            $parcel->status  = 'received-by-pickup-man';
            $parcel->save();

            $this->parcelEvent($parcel->id, 'parcel_received_by_pickup_man_event', $request->note, $user->id);

            DB::commit();

            return $this->responseWithSuccess(__('successfully_received'), '', [], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }


}
