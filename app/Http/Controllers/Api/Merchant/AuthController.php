<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Account\DeliveryManAccount;
use App\Models\Merchant;
use App\Models\LogActivity;
use App\Models\Parcel;
use App\Models\Apikey;
use App\Models\User;
use App\Traits\RandomStringTrait;
use App\Traits\SmsSenderTrait;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendMailTrait;
use App\Http\Resources\Api\LoginActivity;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\Http\Resources\Api\Profile;
use App\Http\Resources\Api\ParcelResource;
use App\Repositories\Interfaces\UserInterface;
use App\Models\Notice;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class AuthController extends Controller
{
    use ApiReturnFormatTrait;
    use RandomStringTrait;
    use SmsSenderTrait;
    use SendMailTrait;

    protected $merchantRepo;
    protected $userRepo;

    public function __construct(MerchantInterface $merchantRepo, UserInterface $userRepo)
    {
        $this->merchantRepo = $merchantRepo;
        $this->userRepo = $userRepo;
    }

    public function signUp(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'company' => 'required|unique:merchants,company',
                'email' => 'required|unique:users,email',
                'phone_number' => 'required|unique:users,phone_number',

            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            if (setting('merchant_verification_status') != 1) {
                if ($user = $this->merchantRepo->registerWithoutVerification($request->all())) {

                    $token = JWTAuth::fromUser($user);
                    $id = $user->id;
                    $profile = $this->profileInfo($id);

                    // Empty data for new merchant
                    $counts = [
                        'total_cod' => 0,
                        'parcels_count' => 0,
                        'processing_count' => 0,
                        'cancelled_count' => 0,
                        'deleted_count' => 0,
                        'partial_delivered_count' => 0,
                        'returned_count' => 0,
                        'delivered' => 0,
                        'current_balance' => 0,
                    ];

                    $data = [
                        'profile' => $profile,
                        'token' => $token,
                        'permissions' => $user->permissions,
                        'counts' => $counts,
                        'parcel' => [],
                        'paginate' => [
                            'total' => 0,
                            'current_page' => 1,
                            'per_page' => 10,
                            'last_page' => 1,
                            'prev_page_url' => null,
                            'next_page_url' => null,
                            'path' => '',
                        ],
                    ];
                    DB::commit();
                    return $this->responseWithSuccess(__('successfully_registered'), [], $data, 200);
                } else {
                    return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
                }
            }

            $user = $this->merchantRepo->tempStore($request);

            if ($user == 'false') {
                return $this->responseWithError(__('unable_to_send_otp'), [], 422);
            }

            if (!$user) {
                return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
            }

            // $data['message'] = 'thank you for register.here is your otp'. ' ' .$user['otp']. ' ' . 'here is your id'. ' ' .$user['temp_id'];
            $data = [
                'otp' => $user['otp'],
                'id' => $user['temp_id'],
            ];

            DB::commit();

            return $this->responseWithSuccess(__('successfully_registered'), [], $data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            if (str_contains($e->getMessage(), 'Default branch')) {
                return $this->responseWithError(__('registration_temporarily_unavailable'), [], 503);
            }
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function otp(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            if ($user = $this->merchantRepo->otpConfirm($request)) {
                $log = [];
                $log['url'] = \Request::fullUrl();
                $log['method'] = \Request::method();
                $log['ip'] = \Request::ip();
                $log['browser'] = $this->getBrowser(\Request::header('user-agent'));
                $log['platform'] = $this->getPlatForm(\Request::header('user-agent'));
                $log['user_id'] = $user->id;
                LogActivity::create($log);

                $data = Sentinel::authenticate($user);

            }

            $data = [
                'info' => $data,
                'api_key' => $user->merchant->api_key,
            ];
            DB::commit();

            return $this->responseWithSuccess(__('successfully_registered'), [], $data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            if (str_contains($e->getMessage(), 'Default branch')) {
                return $this->responseWithError(__('registration_temporarily_unavailable'), [], 503);
            }
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }


    public function otpRequest($id)
    {
        if ($this->merchantRepo->resendOtp($id)):
            $success = __('we_have_send_you_another_otp');
        else:
            $danger = __('unable_to_send_otp');
            return response()->json($danger);
        endif;
    }

    public function activation($email, $activationCode)
    {
        $user = User::whereEmail($email)->first();

        sendMail($user, '', 'verify_email_success', '');

        return redirect()->route('login')->with('success', __('email_verified_successfully'));
    }

    public function login(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();


            if (blank($user)):
                return $this->responseWithError(__('user_not_found'), [], 422);
            endif;

            if ($user->status == \App\Enums\StatusEnum::INACTIVE):
                return $this->responseWithError(__('your_account_is_inactive'), [], 401);
            elseif ($user->status == 2):
                return $this->responseWithError(__('your_account_is_suspend'), [], 401);
            endif;

            if (!Hash::check($request->password, $user->password)):
                return $this->responseWithError(__('password_mismatch'), $validator->errors(), 422);
            endif;

            $credentials = ['email' => $request->email, 'password' => $request->password];


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
            $id = $user->id;

            $profile = $this->profileInfo($id);

            $data = $this->fetchParcelData($user);
            if ($user->user_type == 'merchant_staff') {
                $parcel = Parcel::where('merchant_id', $user->merchant_id)->orWhere('status', 'pending')->latest()->paginate(10);
            } elseif ($user->user_type == 'merchant' && $user->merchant) {
                $parcel = Parcel::where('merchant_id', $user->merchant->id)->orWhere('status', 'pending')->latest()->paginate(10);
            } else {
                $parcel = Parcel::whereRaw('1 = 0')->paginate(10);
            }

            $data = [
                'profile' => $profile ?? [],
                'token' => $token,
                'permissions' => $user->permissions ?? [],
                'counts' => $data,
                'parcel' => ParcelResource::collection($parcel),
                'paginate' => [
                    'total' => $parcel->total(),
                    'current_page' => $parcel->currentPage(),
                    'per_page' => $parcel->perPage(),
                    'last_page' => $parcel->lastPage(),
                    'prev_page_url' => $parcel->previousPageUrl(),
                    'next_page_url' => $parcel->nextPageUrl(),
                    'path' => $parcel->path(),
                ],
            ];
            return $this->responseWithSuccess(__('successfully_login'), [], $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function profile()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }
            $id = $user->id;
            $profile = $this->profileInfo($id);

            $data = [
                'profile' => $profile,
            ];

            return $this->responseWithSuccess(__('successfully_found'), [], $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        DB::beginTransaction();

        try {
            $user = jwtUser();

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|unique:users,email,' . $user->id,

            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('required_field_missing'), $validator->errors(), 422);
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $this->userRepo->updateProfile($request);

            $id = $user->id;
            $profile = $this->profileInfo($id);

            $data = [
                'profile' => $profile,
            ];

            DB::commit();

            return $this->responseWithSuccess(__('successfully_updated'), [], $data, 200);
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
            return $this->responseWithSuccess(__('successfully_logout'), [], 200);
        } catch (JWTException $e) {
            JWTAuth::unsetToken();
            return $this->responseWithError(__('failed_to_logout'), [], 422);
        }
    }

    public function changePassword(Request $request)
    {
        if (isDemoMode()) {
            return $this->responseWithError(__('this_function_is_disabled_in_demo_server'));
        }
        DB::beginTransaction();
        try {
            $data = [];
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|max:50',
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
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $hasher = Sentinel::getHasher();

            $current_password = $request->current_password;
            $password = $request->password;

            if (!$hasher->check($current_password, $user->password)) {
                return back()->with('danger', __('current_password_is_invalid'));
            }

            $user = User::find($user->id);
            $user->password = bcrypt($password);
            $user->last_password_change = date('Y-m-d H:i:s');
            $user->save();
            $data['password'] = $password;

            DB::commit();

            return $this->responseWithSuccess(__('successfully_updated'), [], $data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    public function loginActivity()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->responseWithError(__('unauthorized_user'), '', 404);
            }

            $login_activities = LogActivity::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(2);

            $data = [
                'login_activities' => LoginActivity::collection($login_activities),
                'paginate' => [
                    'total' => $login_activities->total(),
                    'current_page' => $login_activities->currentPage(),
                    'per_page' => $login_activities->perPage(),
                    'last_page' => $login_activities->lastPage(),
                    'prev_page_url' => $login_activities->previousPageUrl(),
                    'next_page_url' => $login_activities->nextPageUrl(),
                    'path' => $login_activities->path(),
                ],
            ];

            return $this->responseWithSuccess(__('successfully_found'), [], $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }
    }

    protected function fetchParcelData($user)
    {
        $defaultCounts = [
            'cod' => 0,
            'current_balance' => 0,
            'total_cod' => format_price(0),
            'parcels_count' => 0,
            'processing_count' => 0,
            'cancelled_count' => 0,
            'deleted_count' => 0,
            'partial_delivered_count' => 0,
            'returned_count' => 0,
            'delivered' => 0,
        ];

        $userPermissions = $user->permissions ?? [];

        if ($user->user_type == 'merchant_staff') {
            if (!$user->staffMerchant) {
                return $defaultCounts;
            }
            $data['cod'] = Parcel::where('merchant_id', $user->merchant_id)
                ->where(function ($query) {
                    $query->whereIn('status', ['delivered', 'delivered-and-verified'])
                        ->orWhere('is_partially_delivered', true);
                })
                ->sum('price');
            $month = date('Y-m');
            $parcels = $user->staffMerchant->parcels()
                ->when(is_array($userPermissions) && !in_array('all_parcel', $userPermissions), function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('created_at', 'like', '%' . $month . '%')->get();

        } elseif ($user->user_type == 'merchant') {
            if (!$user->merchant) {
                return $defaultCounts;
            }
            $data['cod'] = Parcel::where('merchant_id', $user->merchant->id)
                ->where(function ($query) {
                    $query->whereIn('status', ['delivered', 'delivered-and-verified'])
                        ->orWhere('is_partially_delivered', true);
                })
                ->sum('price');
            $month = date('Y-m');
            $parcels = $user->merchant->parcels()->where('created_at', 'like', '%' . $month . '%')->get();
        } else {
            return $defaultCounts;
        }

        $data = $this->get_counts($parcels, $user);

        return $data;
    }

    public function get_counts($parcels, $user)
    {

        if ($user->user_type == 'merchant' && $user->merchant) {
            $parcels = $user->merchant->parcels()->get();
            $data['current_balance'] = $user->merchant->balance ?? 0;
        } elseif ($user->user_type == 'merchant_staff' && $user->staffMerchant) {
            $parcels = Parcel::where('merchant_id', $user->merchant_id)->get();
            $data['current_balance'] = $user->staffMerchant->balance ?? 0;
        } else {
            $parcels = $parcels ?? collect();
            $data['current_balance'] = 0;
        }
        $delivered_cod = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->sum('price');
        $data['total_cod'] = format_price($parcels->where('is_partially_delivered', true)->sum('price') + $delivered_cod);
        $data['parcels_count'] = $parcels->count();
        $data['processing_count'] = $parcels->whereNotIn('status', ['delivered', 'delivered-and-verified', 'cancel', 'returned-to-merchant', 'deleted'])->where('is_partially_delivered', false)->count();
        $data['cancelled_count'] = $parcels->where('status', 'cancel')->count();
        $data['deleted_count'] = $parcels->where('status', 'deleted')->count();
        $data['partial_delivered_count'] = $parcels->where('is_partially_delivered', true)->count();
        $data['returned_count'] = $parcels->where('status', 'returned-to-merchant')->where('is_partially_delivered', false)->count();
        $data['delivered'] = $parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count();

        return $data;
    }

    public function getPlatForm($u_agent)
    {
        $platform = '';
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        return $platform;
    }
    public function getBrowser($u_agent)
    {
        $bname = '';
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/OPR/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        } elseif (preg_match('/Edge/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Trident/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        return $bname;
    }

    public function profileInfo($id)
    {

        $profile = User::where('id', $id)->first();
        if (!$profile) {
            return null;
        }

        $data = [
            'name' => trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')),
            'first_name' => $profile->first_name ?? '',
            'last_name' => $profile->last_name ?? '',
            'phone_number' => $profile->phone_number ?? '',
            'email' => $profile->email ?? '',
            'status' => $profile->status ?? 0,
            'merchant' => $profile->merchant?->company ?? $profile->staffMerchant?->company ?? '',
            'image' => $profile->image ? static_asset($profile->image->image_small_two) : '',
            'created_at' => $profile->created_at ? $profile->created_at->format('d-m-Y H:i:s') : '',
            'updated_at' => $profile->updated_at ? $profile->updated_at->format('d-m-Y H:i:s') : '',
            'address' => $profile->merchant?->address ?? '',
        ];

        return $data;
    }

    public function dashboard()
    {
        try {
            $user = jwtUser();
            $id = $user->id;
            $profile = $this->profileInfo($id);

            $data = $this->fetchParcelData($user);
            if ($user->user_type == 'merchant_staff') {
                $parcel = Parcel::where('merchant_id', $user->merchant_id)->orWhere('status', 'pending')->latest()->paginate(10);
            } elseif ($user->user_type == 'merchant' && $user->merchant) {
                $parcel = Parcel::where('merchant_id', $user->merchant->id)->orWhere('status', 'pending')->latest()->paginate(10);
            } else {
                $parcel = Parcel::whereRaw('1 = 0')->paginate(10);
            }

            $data = [
                'profile' => $profile ?? [],
                'permissions' => $user->permissions ?? [],
                'counts' => $data,
                'parcel' => ParcelResource::collection($parcel),
                'paginate' => [
                    'total' => $parcel->total(),
                    'current_page' => $parcel->currentPage(),
                    'per_page' => $parcel->perPage(),
                    'last_page' => $parcel->lastPage(),
                    'prev_page_url' => $parcel->previousPageUrl(),
                    'next_page_url' => $parcel->nextPageUrl(),
                    'path' => $parcel->path(),
                ],
            ];


            return $this->responseWithSuccess(__('data_retrived_successfully'), [], $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_again'), [], 500);
        }

    }

}
