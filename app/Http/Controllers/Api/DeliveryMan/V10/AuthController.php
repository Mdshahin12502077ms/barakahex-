<?php

namespace App\Http\Controllers\Api\DeliveryMan\V10;

use App\Http\Controllers\Controller;
use App\Models\Account\DeliveryManAccount;
use App\Models\DeliveryMan;
use App\Models\Image as ImageModel;
use App\Models\Parcel;
use App\Models\User;
use App\Models\Page;
use App\Traits\RandomStringTrait;
use App\Traits\SmsSenderTrait;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Carbon;
use Sentinel;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use Image;

class AuthController extends Controller
{
    use ApiReturnFormatTrait;
    use RandomStringTrait;
    use SmsSenderTrait;
    use ImageTrait;

    public function __construct()
    {
        /*$this->middleware('auth:api', ['except' => ['login']]);*/
    }

    public function loginOtp(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
            ]);

            if ($validator->fails()) :
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            endif;

            $user = User::wherePhoneNumber($request->phone_number)->where('user_type','delivery')->first();

            if (blank($user)) :
                return $this->responseWithError(__('user_not_found'), $validator->errors(), 422);
            endif;
            if($user->status == 0) {
                return $this->responseWithError(__('your_account_is_inactive'), $validator->errors(), 422);
            } elseif($user->status == 2) {
                return $this->responseWithError(__('your_account_is_suspend'), $validator->errors(), 422);
            }

            $password = rand(100000,999999);

            \Log::info($user);

            if($this->passwordReset($user, $password)):
                //send otp to user phone number
                $sms_body = 'Use OTP: '.$password.' to login on Delivery Hero App.'. ''. setting('company_name');
                if ($request->phone_number == "01725402187"):
                    DB::commit();
                    return $this->responseWithSuccess(__('an_otp_send_to_your_phone_number'),'', [] ,200);
                elseif($this->send($sms_body, $user->phone_number, 'login-otp', env('PROVIDER'))):
                    DB::commit();
                    return $this->responseWithSuccess(__('an_otp_send_to_your_phone_number'),'', [] ,200);
                else:

                    DB::rollback();
                    return $this->responseWithError(__('unable_to_send_otp_please_try_again'), '', [], 500);
                endif;
            endif;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            $user_id = $request->input('user_id');

            $credential = [];

            if (filter_var($user_id, FILTER_VALIDATE_EMAIL)) {
                $credential['email'] = $user_id;
            } else {
                $credential['phone_number'] = $user_id;
            }


            $user = User::where(function ($query) use ($credential) {
                            $query->where('email', $credential['email'] ?? null)
                                  ->orWhere('phone_number', $credential['phone_number'] ?? null);
                        })
                        ->where('user_type', 'delivery')
                        ->first();


            if (!$user) {
                return $this->responseWithError(__('user_not_found'), [], 422);
            }

            if ($user->status == 0) {
                return $this->responseWithError(__('your_account_is_inactive'), [], 401);
            } elseif ($user->status == 2) {
                return $this->responseWithError(__('your_account_is_suspend'), [], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->responseWithError(__('password_mismatch'), [], 422);
            }

            $credentials = [
                'password' => $request->password,
            ];
            if(isset($credential['email'])){
                    $credentials['email']           = $credential['email'];
            }elseif(isset($credential['phone_number'])){
                    $credentials['phone_number']    = $credential['phone_number'];
            }


            // Attempt to create a token
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return $this->responseWithError(__('unable_to_create_token'), [], 401);
                }
            } catch (JWTException $e) {
                return $this->responseWithError(__('could_not_create_token'), [], 422);
            } catch (ThrottlingException $e) {
                return $this->responseWithError(__('suspicious_activity_on_your_ip') . $e->getDelay() . __('seconds'), [], 500);
            } catch (NotActivatedException $e) {
                return $this->responseWithError(__('you_account_not_activated_check_mail_or_contact_support'), [], 400);
            } catch (\Exception $e) {
                return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
            }
            $result = $this->totalReport($user);



            // Prepare response data
            $data = [
                'profile'           => $this->getProfile($user),
                'currency'          => setting('default_currency'),
                'token'             => $token,
                'earning'           => $result['total_earning'],
                'deposit'           => $result['deposit'],
                'pending_amount'    => $result['pending_amount'],
            ];

            // Return success response with data
            return $this->responseWithSuccess(__('successfully_login'), '', $data, 200);

        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function parcelStatistics(Request $request)
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            //filter
            $today                              = date('Y-m-d');
            $yesterday                          = date('Y-m-d', strtotime('-1 day'));
            $last7days                          = date('Y-m-d', strtotime('-7 days'));
            $last14days                         = date('Y-m-d', strtotime('-14 days'));
            $lastMonthFirstDay                  = date('Y-m-01', strtotime('last month'));
            $lastMonthLastDay                   = date('Y-m-t', strtotime('last month'));
            $last6MonthsFirstDay                = date('Y-m-01', strtotime('-6 months'));
            $last6MonthsLastDay                 = date('Y-m-t', strtotime('-6 months'));
            $last12MonthsFirstDay               = date('Y-m-01', strtotime('-12 months'));
            $last12MonthsLastDay                = date('Y-m-t', strtotime('-12 months'));
            $start_date_one_month_ago           = date('Y-m-d', strtotime('-1 month'));
            $start_date_one_year_ago            = date('Y-m-d', strtotime('-1 year'));
            $now                                = Carbon\Carbon::now();
            $start_date                         = '2000-04-01';
            $end_date                           = date('Y-m-d');
            $filter                             = $request->filter;
            $custom_start_date                  = $request->startDate;
            $custom_end_date                    = $request->endDate;

            switch ($filter) {
                case 'yesterday':
                    $filter_start_date                          = $yesterday;
                    $filter_end_date                            = $today;
                    break;
                case 'last_7_day':
                    $filter_start_date                          = $last7days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_14_day':
                    $filter_start_date                          = $last14days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_month':
                    $filter_start_date                          = $lastMonthFirstDay;
                    $filter_end_date                            = $lastMonthLastDay;
                    break;
                case 'last_6_month':
                    $filter_start_date                          = $last6MonthsFirstDay;
                    $filter_end_date                            = $last6MonthsLastDay;
                    break;
                case 'this_year':
                    $filter_start_date                          = $start_date_one_year_ago;
                    $filter_end_date                            = $today;
                    break;
                case 'last_12_month':
                    $filter_start_date                          = $last12MonthsFirstDay;
                    $filter_end_date                            = $last12MonthsLastDay;
                    break;
                case 'custom':
                    $filter_start_date                          = $custom_start_date;
                    $filter_end_date                            = $custom_end_date;
                    break;
                default:
                    $filter_start_date                          = $today;
                    $filter_end_date                            = $today;
                    break;
            }

            // Prepare response data
            $data = [
                'parcel_statistics' => [
                    'assigned'              => Parcel::whereDate('created_at', '>=', $filter_start_date)
                                                ->whereDate('created_at', '<=', $filter_end_date)->where('delivery_man_id', $user->deliveryMan->id)
                                                ->whereIn('status', ['delivery-assigned', 're-schedule-delivery'])
                                                ->count(),
                    'delivered'             => Parcel::whereDate('created_at', '>=', $filter_start_date)
                                                ->whereDate('created_at', '<=', $filter_end_date)->where('delivery_man_id', $user->deliveryMan->id)
                                                ->whereIn('status', ['delivered', 'delivered-and-verified'])
                                                ->count(),
                    'partially_delivered'   => Parcel::whereDate('created_at', '>=', $filter_start_date)
                                                ->whereDate('created_at', '<=', $filter_end_date)->where('delivery_man_id', $user->deliveryMan->id)
                                                ->where('status', 'partially-delivered')
                                                ->count(),
                    'cancelled'             => Parcel::whereDate('created_at', '>=', $filter_start_date)
                                                ->whereDate('created_at', '<=', $filter_end_date)->where('delivery_man_id', $user->deliveryMan->id)
                                                ->where('status', 'cancel')
                                                ->count(),
                ],
            ];

            return $this->responseWithSuccess(__('statistics_retrived_successfully'), '', $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function financialStatistics(Request $request)
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            //filter
            $today                              = date('Y-m-d');
            $yesterday                          = date('Y-m-d', strtotime('-1 day'));
            $last7days                          = date('Y-m-d', strtotime('-7 days'));
            $last14days                         = date('Y-m-d', strtotime('-14 days'));
            $lastMonthFirstDay                  = date('Y-m-01', strtotime('last month'));
            $lastMonthLastDay                   = date('Y-m-t', strtotime('last month'));
            $last6MonthsFirstDay                = date('Y-m-01', strtotime('-6 months'));
            $last6MonthsLastDay                 = date('Y-m-t', strtotime('-6 months'));
            $last12MonthsFirstDay               = date('Y-m-01', strtotime('-12 months'));
            $last12MonthsLastDay                = date('Y-m-t', strtotime('-12 months'));
            $start_date_one_month_ago           = date('Y-m-d', strtotime('-1 month'));
            $start_date_one_year_ago            = date('Y-m-d', strtotime('-1 year'));
            $now                                = Carbon\Carbon::now();
            $start_date                         = '2000-04-01';
            $end_date                           = date('Y-m-d');
            $filter                             = $request->filter;
            $custom_start_date                  = $request->startDate;
            $custom_end_date                    = $request->endDate;

            switch ($filter) {
                case 'yesterday':
                    $filter_start_date                          = $yesterday;
                    $filter_end_date                            = $today;
                    break;
                case 'last_7_day':
                    $filter_start_date                          = $last7days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_14_day':
                    $filter_start_date                          = $last14days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_month':
                    $filter_start_date                          = $lastMonthFirstDay;
                    $filter_end_date                            = $lastMonthLastDay;
                    break;
                case 'last_6_month':
                    $filter_start_date                          = $last6MonthsFirstDay;
                    $filter_end_date                            = $last6MonthsLastDay;
                    break;
                case 'this_year':
                    $filter_start_date                          = $start_date_one_year_ago;
                    $filter_end_date                            = $today;
                    break;
                case 'last_12_month':
                    $filter_start_date                          = $last12MonthsFirstDay;
                    $filter_end_date                            = $last12MonthsLastDay;
                    break;
                case 'custom':
                    $filter_start_date                          = $custom_start_date;
                    $filter_end_date                            = $custom_end_date;
                    break;
                default:
                    $filter_start_date                          = $today;
                    $filter_end_date                            = $today;
                    break;
            }


            // Calculate financial statistics
            $total_pickup_delivery_commission_income = DeliveryManAccount::whereDate('created_at', '>=', $filter_start_date)
                                                        ->whereDate('created_at', '<=', $filter_end_date)->whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
                                                        ->where('type', 'income')
                                                        ->where('delivery_man_id', $user->deliveryMan->id)
                                                        ->sum('amount');
            $total_pickup_delivery_commission_expense = DeliveryManAccount::whereDate('created_at', '>=', $filter_start_date)
                                                        ->whereDate('created_at', '<=', $filter_end_date)->whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
                                                        ->where('type', 'expense')
                                                        ->where('delivery_man_id', $user->deliveryMan->id)
                                                        ->sum('amount');

            $total_earning                            = $total_pickup_delivery_commission_expense;
            $total_cash_collect_income                = DeliveryManAccount::whereDate('created_at', '>=', $filter_start_date)
                                                        ->whereDate('created_at', '<=', $filter_end_date)->where('source', 'cash_collection')
                                                        ->where('type', 'income')
                                                        ->where('delivery_man_id', $user->deliveryMan->id)
                                                        ->sum('amount');
            $total_cash_collect_expense               = DeliveryManAccount::whereDate('created_at', '>=', $filter_start_date)
                                                        ->whereDate('created_at', '<=', $filter_end_date)->where('source', 'cash_given_to_staff')
                                                        ->where('type', 'expense')
                                                        ->where('delivery_man_id', $user->deliveryMan->id)
                                                        ->sum('amount');
            $total_expense          = $total_earning + $total_cash_collect_expense;
            $pending_amount         = number_format($total_cash_collect_income - $total_expense, 2);
            $commission_received    = $total_earning;
            $cash_collect           = $total_cash_collect_income;
            $deposit                = $total_cash_collect_expense;


            // Prepare response data
            $data = [
                'financial_statistics' => [
                    'cash_collection'       => $cash_collect,
                    'commission_received'   => $commission_received,
                    'deposit'               => $deposit,
                    'pending_amount'        => $pending_amount,
                ],
            ];

            return $this->responseWithSuccess(__('statistics_retrived_successfully'), '', $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        }
    }

    public function profile()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }
            $data['id'] = $user->id;

            $data       = $this->getProfile($user);

            return $this->responseWithSuccess(__('successfully_found'), '', $data, 200);
        }catch (\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        \DB::beginTransaction();

        try{
            $validator = Validator::make($request->all(), [
                'first_name'    => 'required|max:50',
                'last_name'     => 'required|max:50',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }
            $data                  = $request->all();
            $user->first_name      = $data['first_name'];
            $user->last_name       = $data['last_name'];
            $user->email           = $data['email'];
            if (isset($data['image'] )) {
                $response                     = $this->saveImage($data['image'] ,'image');
                $images                       = $response['images'];
                $user->image_id               = $images;
            }
            $user->phone_number               = $data['phone_number'];
            $user->save();
            $delivery_man                         = DeliveryMan::find($user->deliveryMan->id);
            $delivery_man->address                = $data['address'];
            $delivery_man->phone_number           = $data['phone_number'];

            if (isset($data['driving_license'] )) {
                $response                                    = $this->saveImage($data['driving_license'] ,'image');
                $images                                      = $response['images'];
                $delivery_man->driving_license               = $images;
            }
            $delivery_man->save();

            $datas = $this->getProfile($user);

            DB::commit();

            return $this->responseWithSuccess(__('successfully_updated'), '', $datas, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError($e->getMessage(), '', [], 500);
        }
    }

    public function changePassword(Request $request)
    {
        // Log::info('change password error: '.isDemoMode());
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        \DB::beginTransaction();
        try{

            $data                   = [];
            $validator              = Validator::make($request->all(), [
                'current_password'  => 'required|max:50',
                'password' => [
                        'required',
                        'confirmed',
                        'min:6',
                    ],
                ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }


            $hasher             = \Sentinel::getHasher();

            $current_password   = $request->current_password;
            $password           = $request->password;
            if (!$hasher->check($current_password, $user->password)) {
                return $this->responseWithError(__('current_password_is_invalid'), '' , 404);

            }

            $user                           = User::find($user->id);
            $user->password                 = bcrypt($password);
            $user->last_password_change     = date('Y-m-d H:i:s');

            $user->save();
            $data['password']               = $password;

            DB::commit();

            return $this->responseWithSuccess(__('successfully_updated'), '', $data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function logout()
    {
        try {
            Sentinel::logout();
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->responseWithSuccess(__('successfully_logout'),'', [] ,200);
        } catch (JWTException $e) {
            JWTAuth::unsetToken();
            return $this->responseWithError(__('failed_to_logout'), '', [], 422);
        }
    }

    public function getProfile($user)
    {
        $data['id']                = $user->id;
        $data['name']              = $user->first_name .' '.$user->last_name;
        $data['first_name']        = $user->first_name;
        $data['last_name']         = $user->last_name;
        $data['email']             = $user->email;
        $data['phone_number']      = $user->deliveryMan['phone_number'];
        $data['image']             = getFileLink('80X80', $user->image_id);
        $data['address']           = $user->deliveryMan['address'];
        $data['driving_license']   = getFileLink('80X80', $user->deliveryMan->driving_license);
        $data['balance']           = number_format($user->deliveryMan->balance($user->deliveryMan->id),2) .' ' . setting('default_currency');
        return $data;
    }

    function validEmail($str) {
        return (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? TRUE : FALSE;
    }

    public function forgotPasswordOtp(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            $user      = User::wherePhoneNumber($request->phone_number)->first();

            if ($user == null) {
                return $this->responseWithError(__('user_not_found'), $validator->errors(), 422);
            }

            if (blank($user->phone_number)) {
                return $this->responseWithError(__('invalid_phone_number'), $validator->errors(), 422);
            }

            $otp = rand(1000,9999);
            if($this->passwordResetOtp($user, $otp)):
                //send password to user phone number
                $sms_body = 'Use OTP:'.$otp.' to verify your reset password confirmation.';
                if($this->test($sms_body, $user->phone_number, 'reset-password', env('PROVIDER'))):
                    DB::commit();
                    return $this->responseWithSuccess(__('reset_password_confirmation_otp_send'),[] ,200);
                else:
                    DB::rollback();
                    return $this->responseWithError(__('unable_to_create_otp_please_try_again'), [], 500);
                endif;
            endif;

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function forgotPasswordPost(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        DB::beginTransaction();
        try {
            $validator         = Validator::make($request->all(), [
                'phone_number' => 'required',
                'otp'          => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            $user               = User::wherePhoneNumber($request->phone_number)->first();
            if ($user->otp != $request->otp):
                return $this->responseWithError(__('otp_did_not_match_please_provide_the_valid_otp'), [], 422);
            endif;

            if (blank($user)) {
                return $this->responseWithError(__('invalid_phone_number'), $validator->errors(), 422);
            }

            $password = $this->generate_random_string(6);

            if($this->passwordReset($user, $password)):
                //send password to user phone number
                $sms_body = 'Use '.$password.' as your password to login on Delivery Hero App. DeliX';

                if($this->test($sms_body, $user->phone_number, 'reset-password', env('PROVIDER'))):
                    DB::commit();
                    return $this->responseWithSuccess(__('password_reset_successful_and_send_to_your_phone_number'),[] ,200);
                else:
                    DB::rollback();
                    return $this->responseWithError(__('unable_to_reset_password_please_try_again'), [], 500);
                endif;
            endif;

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function passwordReset($user, $password)
    {
        try {
            $user->password             = Hash::make($password);
            $user->last_password_change = date('Y-m-d H:i:s');
            $user->save();

            return true;
        } catch (\Exception $e){
            return false;
        }
    }

    public function passwordResetOtp($user, $otp)
    {
        try {
            $user->otp   = $otp;
            $user->save();

            return true;
        } catch (\Exception $e){
            return false;
        }
    }
    //v10 updates
    public function paymentLogs(Request $request){
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page         = $request->page ?? 1;
            $offset       = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit        = \Config::get('parcel.api_paginate');
            $delivery_man = DeliveryMan::find($user->deliveryMan->id);
            $logs         = $delivery_man->paymentLogs()->get();
            $logs         = $this->formatLogs($logs);
            $logs         = $logs->sortDesc()->skip($offset)->take($limit)->flatten();

            $data = [
                'log' => $logs,
            ];


            return $this->responseWithSuccess(__('successfully_found'),'', $data ,200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    //v10 updates
    public function cashDeposits(Request $request){
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            //filter
            $today                              = date('Y-m-d');
            $yesterday                          = date('Y-m-d', strtotime('-1 day'));
            $last7days                          = date('Y-m-d', strtotime('-7 days'));
            $last14days                         = date('Y-m-d', strtotime('-14 days'));
            $lastMonthFirstDay                  = date('Y-m-01', strtotime('last month'));
            $lastMonthLastDay                   = date('Y-m-t', strtotime('last month'));
            $last6MonthsFirstDay                = date('Y-m-01', strtotime('-6 months'));
            $last6MonthsLastDay                 = date('Y-m-t', strtotime('-6 months'));
            $last12MonthsFirstDay               = date('Y-m-01', strtotime('-12 months'));
            $last12MonthsLastDay                = date('Y-m-t', strtotime('-12 months'));
            $start_date_one_month_ago           = date('Y-m-d', strtotime('-1 month'));
            $start_date_one_year_ago            = date('Y-m-d', strtotime('-1 year'));
            $now                                = Carbon\Carbon::now();
            $start_date                         = '2000-04-01';
            $end_date                           = date('Y-m-d');
            $filter                             = $request->filter;
            $custom_start_date                  = $request->startDate;
            $custom_end_date                    = $request->endDate;


            switch ($filter) {
                case 'yesterday':
                    $filter_start_date                          = $yesterday;
                    $filter_end_date                            = $today;
                    break;
                case 'last_7_day':
                    $filter_start_date                          = $last7days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_14_day':
                    $filter_start_date                          = $last14days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_month':
                    $filter_start_date                          = $lastMonthFirstDay;
                    $filter_end_date                            = $lastMonthLastDay;
                    break;
                case 'last_6_month':
                    $filter_start_date                          = $last6MonthsFirstDay;
                    $filter_end_date                            = $last6MonthsLastDay;
                    break;
                case 'this_year':
                    $filter_start_date                          = $start_date_one_year_ago;
                    $filter_end_date                            = $today;
                    break;
                case 'last_12_month':
                    $filter_start_date                          = $last12MonthsFirstDay;
                    $filter_end_date                            = $last12MonthsLastDay;
                    break;
                case 'custom':
                    $filter_start_date                          = $custom_start_date;
                    $filter_end_date                            = $custom_end_date;
                    break;
                default:
                    $filter_start_date                          = $today;
                    $filter_end_date                            = $today;
                    break;
            }

            $delivery_man   = DeliveryMan::find($user->deliveryMan->id);
            $logs           = $delivery_man->paymentLogs()->whereDate('created_at', '>=', $filter_start_date)
                            ->whereDate('created_at', '<=', $filter_end_date)->where('source','cash_given_to_staff')->get();

            $logs = $this->formatDepositLogs($logs);
            $logs = $logs->sortDesc()->skip($offset)->take($limit)->flatten();

            $data = [
                'deposits' => $logs,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data ,200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function pendingAmount(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            //filter
            $today                              = date('Y-m-d');
            $yesterday                          = date('Y-m-d', strtotime('-1 day'));
            $last7days                          = date('Y-m-d', strtotime('-7 days'));
            $last14days                         = date('Y-m-d', strtotime('-14 days'));
            $lastMonthFirstDay                  = date('Y-m-01', strtotime('last month'));
            $lastMonthLastDay                   = date('Y-m-t', strtotime('last month'));
            $last6MonthsFirstDay                = date('Y-m-01', strtotime('-6 months'));
            $last6MonthsLastDay                 = date('Y-m-t', strtotime('-6 months'));
            $last12MonthsFirstDay               = date('Y-m-01', strtotime('-12 months'));
            $last12MonthsLastDay                = date('Y-m-t', strtotime('-12 months'));
            $start_date_one_month_ago           = date('Y-m-d', strtotime('-1 month'));
            $start_date_one_year_ago            = date('Y-m-d', strtotime('-1 year'));
            $now                                = Carbon\Carbon::now();
            $start_date                         = '2000-04-01';
            $end_date                           = date('Y-m-d');
            $filter                             = $request->filter;
            $custom_start_date                  = $request->startDate;
            $custom_end_date                    = $request->endDate;


            switch ($filter) {
                case 'yesterday':
                    $filter_start_date                          = $yesterday;
                    $filter_end_date                            = $today;
                    break;
                case 'last_7_day':
                    $filter_start_date                          = $last7days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_14_day':
                    $filter_start_date                          = $last14days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_month':
                    $filter_start_date                          = $lastMonthFirstDay;
                    $filter_end_date                            = $lastMonthLastDay;
                    break;
                case 'last_6_month':
                    $filter_start_date                          = $last6MonthsFirstDay;
                    $filter_end_date                            = $last6MonthsLastDay;
                    break;
                case 'this_year':
                    $filter_start_date                          = $start_date_one_year_ago;
                    $filter_end_date                            = $today;
                    break;
                case 'last_12_month':
                    $filter_start_date                          = $last12MonthsFirstDay;
                    $filter_end_date                            = $last12MonthsLastDay;
                    break;
                case 'custom':
                    $filter_start_date                          = $custom_start_date;
                    $filter_end_date                            = $custom_end_date;
                    break;
                default:
                    $filter_start_date                          = $today;
                    $filter_end_date                            = $today;
                    break;
            }

            $delivery_man = DeliveryMan::find($user->deliveryMan->id);

            $logs = DeliveryManAccount::whereDate('created_at', '>=', $filter_start_date)
                    ->whereDate('created_at', '<=', $filter_end_date)->where('delivery_man_id', $delivery_man->id)
                    ->where('source', 'cash_collection')
                    ->whereNotIn('parcel_id', function($query) {
                        $query->select('parcel_id')
                            ->from('delivery_man_accounts')
                            ->where('source', 'cash_given_to_staff');
                    })
                    ->get();

            $logs = $this->formatLogs($logs);

            $logs = $logs->sortDesc()->skip($offset)->take($limit)->flatten();

            $data = [
                'cash_deposit' => $logs,
            ];


            return $this->responseWithSuccess(__('successfully_found'),'', $data ,200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function earning(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $page = $request->page ?? 1;

            $offset = ( $page * \Config::get('parcel.api_paginate') ) - \Config::get('parcel.api_paginate');
            $limit  = \Config::get('parcel.api_paginate');

            //filter
            $today                              = date('Y-m-d');
            $yesterday                          = date('Y-m-d', strtotime('-1 day'));
            $last7days                          = date('Y-m-d', strtotime('-7 days'));
            $last14days                         = date('Y-m-d', strtotime('-14 days'));
            $lastMonthFirstDay                  = date('Y-m-01', strtotime('last month'));
            $lastMonthLastDay                   = date('Y-m-t', strtotime('last month'));
            $last6MonthsFirstDay                = date('Y-m-01', strtotime('-6 months'));
            $last6MonthsLastDay                 = date('Y-m-t', strtotime('-6 months'));
            $last12MonthsFirstDay               = date('Y-m-01', strtotime('-12 months'));
            $last12MonthsLastDay                = date('Y-m-t', strtotime('-12 months'));
            $start_date_one_month_ago           = date('Y-m-d', strtotime('-1 month'));
            $start_date_one_year_ago            = date('Y-m-d', strtotime('-1 year'));
            $now                                = Carbon\Carbon::now();
            $start_date                         = '2000-04-01';
            $end_date                           = date('Y-m-d');
            $filter                             = $request->filter;
            $custom_start_date                  = $request->startDate;
            $custom_end_date                    = $request->endDate;


            switch ($filter) {
                case 'yesterday':
                    $filter_start_date                          = $yesterday;
                    $filter_end_date                            = $today;
                    break;
                case 'last_7_day':
                    $filter_start_date                          = $last7days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_14_day':
                    $filter_start_date                          = $last14days;
                    $filter_end_date                            = $today;
                    break;
                case 'last_month':
                    $filter_start_date                          = $lastMonthFirstDay;
                    $filter_end_date                            = $lastMonthLastDay;
                    break;
                case 'last_6_month':
                    $filter_start_date                          = $last6MonthsFirstDay;
                    $filter_end_date                            = $last6MonthsLastDay;
                    break;
                case 'this_year':
                    $filter_start_date                          = $start_date_one_year_ago;
                    $filter_end_date                            = $today;
                    break;
                case 'last_12_month':
                    $filter_start_date                          = $last12MonthsFirstDay;
                    $filter_end_date                            = $last12MonthsLastDay;
                    break;
                case 'custom':
                    $filter_start_date                          = $custom_start_date;
                    $filter_end_date                            = $custom_end_date;
                    break;
                default:
                    $filter_start_date                          = $today;
                    $filter_end_date                            = $today;
                    break;
            }


            $delivery_man = DeliveryMan::find($user->deliveryMan->id);

            $logs = DeliveryManAccount::whereDate('created_at', '>=', $filter_start_date)
                    ->whereDate('created_at', '<=', $filter_end_date)->where('delivery_man_id', $delivery_man->id)
                    ->whereIn('source', ['parcel_delivery', 'pickup_commission'])->get();

            $logs = $this->formatLogs($logs);

            $logs = $logs->sortDesc()->skip($offset)->take($limit)->flatten();

            $data = [
                'earning' => $logs,
            ];

            return $this->responseWithSuccess(__('successfully_found'), '', $data ,200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), '', [], 500);
        }
    }

    public function formatLogs($logs){
        foreach ($logs as $log):
            $log['id']          = $log->id;
            $log['source']      = __($log->source);
            $log['details']     = __($log->details);
            $log['date']        = date('d-M-Y',strtotime($log->date));
            $log['amount']      = number_format($log->amount, 2);
            $log['parcel_no']   = @$log->parcel->parcel_no;
            $log['customer_name']   = @$log->parcel->customer_name;


            unset($log->created_at);
            unset($log->updated_at);
            unset($log->company_account_id);
            unset($log->delivery_man_id);
            unset($log->parcel_id);
            unset($log->parcel);
            unset($log->balance);
        endforeach;

        return $logs;
    }

    public function formatDepositLogs($logs){
        $user = JWTAuth::parseToken()->authenticate();
        foreach ($logs as $log):
            $log['id']          = $log->id;
            $log['source']      = __($log->source);
            $log['details']     = __($log->details);
            $log['method']      = @$log->companyAccount->account->method;
            $log['date']        = date('d-M-Y',strtotime($log->date));
            $log['amount']      = number_format($log->amount, 2);
            $log['to_whom']     = @$log->companyAccount->account->user->first_name.' '.@$log->companyAccount->account->user->last_name;
            $log['by']          = @$user->first_name. ' ' .$user->last_name;

            unset($log->created_at);
            unset($log->updated_at);
            unset($log->company_account_id);
            unset($log->delivery_man_id);
            unset($log->parcel_id);
            unset($log->parcel);
            unset($log->balance);
            unset($log->companyAccount);
        endforeach;

        return $logs;
    }

    public function updateProfileImage(Request $request){
        DB::beginTransaction();

        try{
            $validator = Validator::make($request->all(), [
                'image'    => 'required|mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP|max:5120',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }
            if (!blank($request->file('image'))) {
                $requestImage           = $request->file('image');
                $fileType               = $requestImage->getClientOriginalExtension();

                $originalImage      = date('YmdHis') . "_original_" . rand(1, 50) . '.' . $fileType;
                $imageSmallOne      = date('YmdHis') . "image_small_one" . rand(1, 50) . '.' . $fileType;
                $imageSmallTwo      = date('YmdHis') . "image_small_two" . rand(1, 50) . '.' . $fileType;
                $imageSmallThree    = date('YmdHis') . "image_small_three" . rand(1, 50) . '.' . $fileType;

                $directory              = 'admin/profile-images/';

                if(!is_dir($directory)) {
                    mkdir($directory);
                }

                $originalImageUrl       = $directory . $originalImage;
                $imageSmallOneUrl       = $directory . $imageSmallOne;
                $imageSmallTwoUrl       = $directory . $imageSmallTwo;
                $imageSmallThreeUrl     = $directory . $imageSmallThree;

                Image::make($requestImage)->save($originalImageUrl, 80);
                Image::make($requestImage)->fit(32, 32)->save($imageSmallOneUrl, 80);
                Image::make($requestImage)->fit(40, 40)->save($imageSmallTwoUrl, 80);
                Image::make($requestImage)->fit(128, 128)->save($imageSmallThreeUrl, 80);

                $image                          = new ImageModel();
                $image->original_image          = $originalImageUrl;
                $image->image_small_one         = $imageSmallOneUrl;
                $image->image_small_two         = $imageSmallTwoUrl;
                $image->image_small_three       = $imageSmallThreeUrl;
                $image->save();

            }


            $user->image_id      = $image->id ?? null;
            $user->save();

            $data = $this->getProfile($user);

            DB::commit();

            return $this->responseWithSuccess(__('successfully_updated'), $data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function imageUpload($image, $type, $delivery_man_id = '')
    {
        if($delivery_man_id != ''):
            $delivery = DeliveryMan::find($delivery_man_id);
            if($delivery->driving_license != "" && file_exists($delivery->driving_license)):
                unlink($delivery->driving_license);
            endif;
        endif;

        $requestImage           = $image;
        $fileType               = $requestImage->getClientOriginalExtension();
        $originalImage          = date('YmdHis') .'-'. $type . rand(1, 50) . '.' . $fileType;
        $directory              = 'admin/'.$type.'/';

        $storagePath = public_path($directory);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        $originalImageUrl       = $storagePath . $originalImage;
        Image::make($requestImage)->save($originalImageUrl, 80);
        return static_asset($directory . $originalImage);
    }

    public function language(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            $languageFilePath   = base_path('lang');
            if ($request->bn) {
                $language       = file_get_contents($languageFilePath . '/bn.json');
            } else {
                $language       = file_get_contents($languageFilePath . '/en.json');
            }

            $languageArray      = json_decode($language, true);

            $data = [
                'language' => $languageArray,
            ];

            return $this->responseWithSuccess('language_retrieved_successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function notification(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }


            $notifications = Notification::select('notifications.*','nu.id as notification_user_id')
                                ->join('notification_users as nu','nu.notification_id','=','notifications.id')
                                ->where('nu.user_id', $user->id)->where('nu.is_read',0)
                                ->groupBy('nu.notification_id')
                                ->latest()->get();

            $notificationCount = NotificationUser::where('user_id', $user->id)->where('is_read', 0)->count();


            $data = [
                'notifications'           => $notifications,
                'notificationCount'       => $notificationCount,
            ];

            return $this->responseWithSuccess('Notification info retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }

    }

    public function updateNotification($id): \Illuminate\Http\JsonResponse
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return $this->responseWithError(__('unauthorized_user'), '' , 404);
        }

        try {
            $notification           = NotificationUser::where('notification_id', $id)->first();
            $notification->is_read  = 1;
            $notification->save();
            $data                   = Notification::where('id', $notification->notification_id)->first();
            $data = [
                'url'           => $data->url,
            ];

            return $this->responseWithSuccess('Notification info retrieved successfully', [], $data);

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function privacyPolicy(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '' , 404);
            }

            // $data           = setting('rider_privacy_policy');

            $data           = [
                'url' => setting('rider_privacy_policy'),
            ];


            return $this->responseWithSuccess('Privacy & policy retrieved successfully', [], $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }

    }

    public function report(){
        try{
        $user = JWTAuth::parseToken()->authenticate();
        $this->totalReport($user);

        $result = $this->totalReport($user);



            $data = [
                'earning'           => $result['total_earning'],
                'deposit'           => $result['deposit'],
                'pending_amount'    => $result['pending_amount'],
            ];

            return $this->responseWithSuccess(__('report_retrived_successfully'), '', $data, 200);

        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function totalReport($user)
    {
        // Calculate financial statistics
        $total_pickup_delivery_commission_income   = DeliveryManAccount::whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
                                                                    ->where('type', 'income')
                                                                    ->where('delivery_man_id', $user->deliveryMan->id)
                                                                    ->sum('amount');
        $total_pickup_delivery_commission_expense  = DeliveryManAccount::whereIn('source', ['pickup_commission', 'parcel_delivery', 'parcel_return'])
                                                                    ->where('type', 'expense')
                                                                    ->where('delivery_man_id', $user->deliveryMan->id)
                                                                    ->sum('amount');

        $total_earning                              = $total_pickup_delivery_commission_expense;

        $total_cash_collect_income                  = DeliveryManAccount::where('source', 'cash_collection')
                                                        ->where('type', 'income')
                                                        ->where('delivery_man_id', $user->deliveryMan->id)
                                                        ->sum('amount');
        $total_cash_collect_expense                 = DeliveryManAccount::where('source', 'cash_given_to_staff')
                                                        ->where('type', 'expense')
                                                        ->where('delivery_man_id', $user->deliveryMan->id)
                                                        ->sum('amount');

        $total_expense                      = $total_earning + $total_cash_collect_expense;

        $data['pending_amount']             = number_format($total_cash_collect_income - $total_expense, 2);


        // $data['pending_amount']             = number_format($total_cash_collect_income - $total_cash_collect_expense, 2) . ' ' . __('tk');
        $data['total_earning']              = $total_earning;
        $data['commission_received']        = $total_pickup_delivery_commission_income;
        $data['cash_collect']               = $total_cash_collect_income;
        $data['deposit']                    = $total_cash_collect_expense;

        return $data;
    }

    public function pusherCredential()
    {
        try{
        $credentials = [
            'PUSHER_APP_KEY'        => env('PUSHER_APP_KEY'),
            'PUSHER_APP_SECRET'     => env('PUSHER_APP_SECRET'),
            'PUSHER_APP_ID'         => env('PUSHER_APP_ID'),
            'PUSHER_APP_CLUSTER'    => env('PUSHER_APP_CLUSTER'),
        ];


        $data =[
            'credential' => $credentials,
        ];

        return $this->responseWithSuccess(__('pusher_retrived_successfully'), '', $data, 200);

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 500);
        };
    }



}
