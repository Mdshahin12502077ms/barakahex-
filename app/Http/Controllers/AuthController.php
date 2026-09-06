<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginPostRequest;
use App\Http\Requests\Admin\Merchant\MerchantStoreRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordPostRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordPostRequest;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\LogActivity as LogActivityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\SendNotification;
use App\Traits\SendMailTrait;
use ReCaptcha\ReCaptcha;
use App\Models\User;

class AuthController extends Controller
{
    protected $merchants;
    use SendNotification, SendMailTrait;

    public function __construct(MerchantInterface $merchants)
    {
        $this->merchants = $merchants;
    }

    public function loginForm()
    {
        return view('auth.login');
    }
    public function login(LoginPostRequest $request)
    {

        $user = User::where('email', $request->email)->orWhere('phone_number', $request->email)->first();

        if (blank($user)) {
            return redirect()->back()->withInput()->with('danger', __('user_not_found'));
        }
        if ($user->status == \App\Enums\StatusEnum::INACTIVE) {
            return redirect()->back()->withInput()->with('danger', __('your_account_is_inactive'));
        } elseif ($user->status == 2) {
            return redirect()->back()->withInput()->with('danger', __('your_account_is_suspend'));
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return redirect()->back()->withInput()->with('danger', __('password_wrong'));
        }
        if (setting('is_recaptcha_activated')) {
            $recaptcha = new ReCaptcha(setting('recaptcha_secret'));
            $resp = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
            if (!$resp->isSuccess()) {
                return redirect()->back()->withInput()->with('danger', __('please_verify_that_you_are_not_a_robot'));
            }
        }

        $log = [];
        $log['url'] = \Request::fullUrl();
        $log['method'] = \Request::method();
        $log['ip'] = \Request::ip();
        $log['browser'] = $this->getBrowser(\Request::header('user-agent'));
        $log['platform'] = $this->getPlatForm(\Request::header('user-agent'));
        $log['user_id'] = $user->id;
        LogActivityModel::create($log);

        $remember_me = $request->has('remember_me') ? true : false;
        Sentinel::authenticate($user, $remember_me);

        if ($user->user_type == "staff" || $user->user_type == "admin" || $user->user_type == "super_admin") {
            return redirect()->route('dashboard');
        } elseif ($user->user_type == "merchant") {
            return redirect()->route('merchant.dashboard');
        } elseif ($user->user_type == "merchant_staff") {
            return redirect()->route('merchant.staff.dashboard');
        } elseif ($user->user_type == "delivery_man" || $user->user_type == "delivery") {
            return redirect()->route('deliveryman.dashboard');
        }
    }

    public function logout()
    {
        Sentinel::logout();
        return redirect()->route('home');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(MerchantStoreRequest $request)
    {
        if (setting('is_recaptcha_activated')) {
            $recaptcha = new ReCaptcha(setting('recaptcha_secret'));
            $resp = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
            if (!$resp->isSuccess()) {
                return redirect()->back()->withInput()->with('danger', __('please_verify_that_you_are_not_a_robot'));
            }
        }
        if (setting('merchant_verification_status') != 1):
            try {
                if ($user = $this->merchants->registerWithoutVerification($request->all())):
                    Sentinel::authenticate($user);
                    $users = [];
                    $details = __('new_merchant_registered') . '-' . $user->merchant->company;
                    $users = User::where('user_type', 'staff')->get();
                    $permissions = ['merchant_read'];
                    $title = __('new_merchant_registered') . '-' . $user->merchant->company;
                    $this->sendNotification($title, $users, $details, $permissions, 'success', url('admin/merchant/personal-info/' . $user->merchant->id), '');

                    return redirect()->route('merchant.dashboard')->with('success', __('registration_successful'));
                else:
                    return back()->withInput()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), 'Default branch')) {
                    return back()->withInput()->with('danger', __('registration_temporarily_unavailable'));
                }
                throw $e;
            }
        endif;

        if ($id = $this->merchants->tempStore($request)):
            if ($id == 'false'):
                return back()->withInput()->with('danger', __('unable_to_send_otp'));
            else:
                $success = __('check_your_phone_for_otp');
                return view('auth.confirm-otp', compact('id', 'success'));
            endif;
        else:
            return back()->withInput()->with('danger', __('something_went_wrong_please_try_again'));
        endif;
    }

    public function otpConfirm()
    {
        return view('errors.404');
    }

    public function otpConfirmPost(Request $request)
    {
        try {
            if ($user = $this->merchants->otpConfirm($request)):

                $log = [];
                $log['url'] = \Request::fullUrl();
                $log['method'] = \Request::method();
                $log['ip'] = \Request::ip();
                $log['browser'] = $this->getBrowser(\Request::header('user-agent'));
                $log['platform'] = $this->getPlatForm(\Request::header('user-agent'));
                $log['user_id'] = $user->id;
                LogActivityModel::create($log);

                Sentinel::authenticate($user);

                $users = [];
                $details = __('new_merchant_registered') . '-' . $user->merchant->company;
                $users = User::where('user_type', 'staff')->get();
                $permissions = ['merchant_read'];
                $title = __('new_merchant_registered') . '-' . $user->merchant->company;
                $this->sendNotification($title, $users, $details, $permissions, 'success', url('admin/merchant/personal-info/' . $user->merchant->id), '');

                return redirect()->route('merchant.dashboard')->with('success', __('registration_successful'));
            else:
                $id = $request['id'];
                $danger = __('otp_mismatch');
                return view('merchant.auth.confirm-otp', compact('id', 'danger'));
            endif;
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'Default branch')) {
                $id = $request['id'] ?? null;
                $danger = __('registration_temporarily_unavailable');
                return $id ? view('merchant.auth.confirm-otp', compact('id', 'danger')) : back()->withInput()->with('danger', $danger);
            }
            throw $e;
        }
    }

    public function otpRequest($id)
    {
        if ($this->merchants->resendOtp($id)):
            $success = __('we_have_send_you_another_otp');
        else:
            $danger = __('unable_to_send_otp');
            return response()->json($danger);
        endif;
    }

    public function activation($email, $activationCode)
    {
        $user = User::whereEmail($email)->first();

        $data = [
            'subject' => __('welcome_email'),
            'user' => $user,
            'login_link' => url('/login'),
            'template_title' => 'welcome_email',
        ];


        $this->sendMail($user->email, 'merchant.auth.mail.registration-success-email', $data);

        return redirect()->route('login')->with('success', __('email_verified_successfully'));
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



    public function forgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function forgotPasswordPost(ForgotPasswordPostRequest $request)
    {

        $user = User::whereEmail($request->email)->first();

        if (blank($user)) {
            return redirect()->back()->with([
                'danger' => __('invalid_email'),
            ]);
        }

        if (Reminder::exists($user)):
            $remainder = Reminder::where('user_id', $user->id)->first();
        else:
            $remainder = Reminder::create($user);
        endif;



        $data = [
            'subject' => "Reset Password",
            'user' => $user,
            'reset_link' => url('/') . '/reset/' . $user->email . '/' . $remainder->code,
            'template_title' => 'password_reset',
        ];



        //send a mail to user
        $this->sendMail($request->email, 'admin.auth.mail.forgot-password-email', $data);


        return redirect()->back()->with([
            'success' => __('reset_link_is_send_to_mail'),
        ]);
    }

    public function resetPassword($email, $resetCode)
    {
        $user = User::byEmail($email);

        if ($reminder = Reminder::exists($user, $resetCode)):
            return view('auth.reset-password', ['email' => $email, 'resetCode' => $resetCode]);
        else:
            return redirect()->route('login');
        endif;
    }

    public function PostResetPassword(ResetPasswordPostRequest $request, $email, $resetCode)
    {

        $user = User::byEmail($email);
        if ($reminder = Reminder::exists($user, $resetCode)) {
            Reminder::complete($user, $resetCode, $request->password);

            $data = [
                'subject' => "Recovery Mail",
                'user' => $user,
                "password" => $request->password,
                'template_title' => 'Recovery_mail',
            ];



            //send a mail to user
            $this->sendMail($user->email, 'admin.auth.mail.reset-success-email', $data);

            return redirect()->route('login')->with('success', __('you_can_login_with_new_password'));
        } else {
            return redirect()->route('login');
        }
    }
}
