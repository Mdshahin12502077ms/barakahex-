<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Account\CompanyAccount;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Repositories\Interfaces\UserInterface;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Repositories\NotificationRepository;
use App\DataTables\Admin\PayoutLogDataTable;
use App\Repositories\Interfaces\Admin\BankAccountInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CommonController extends Controller
{
    protected $users;
    protected $bank_accounts;

    public function __construct(UserInterface $users, BankAccountInterface $bank_accounts)
    {
        $this->users                   = $users;
        $this->bank_accounts           = $bank_accounts;

    }
    public function deleteModal($id)
    {
    	return 'cdfsds';
    }

    public function modeChange()
    {
        $mode               = Session::get('mode');
        if($mode == 'dark-mode'):
            Session::put('mode', 'light-mode');
        else:
            Session::put('mode', 'dark-mode');
        endif;
        return response()->json('success');
    }

    public function profile()
    {
        return view('common.profile.staff.staff-profile');
    }


    public function paymentLogs(PayoutLogDataTable $dataTable)
    {
        $statements = CompanyAccount::where('user_id', Sentinel::getUser()->id)->orderby('id', 'desc')->paginate(9);

        return $dataTable->render('common.profile.staff.payment-logs.payment-logs', compact('statements'));

    }

    public function notification()
    {
        return view('common.profile.staff.staff-notification');
    }

    public function accountActivity()
    {
        $login_activities = LogActivity::where('user_id', Sentinel::getUser()->id)->orderBy('id', 'desc')->paginate(9);
        return view('common.profile.staff.staff-account-activity', compact('login_activities'));
    }

    public function securitySetting()
    {
        return view('common.profile.staff.staff-security-settings');
    }


    public function changePassword(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }

        $request->validate([
            'new_password' => 'required|min:6',
        ], [
            'new_password.required' => __('The new password is required.'),
            'new_password.min' => __('The new password must be at least 6 characters.'),
        ]);

        try {
            $hasher              = Sentinel::getHasher();

            $current_password    = $request->current_password;
            $password            = $request->new_password;
            $user                = Sentinel::getUser();


            if (!$hasher->check($current_password, $user->password)) {
                return back()->with('danger', __('current_password_is_invalid'));
            }

            $user                       = User::find(Sentinel::getUser()->id);
            $user->password             = bcrypt($password);
            $user->last_password_change = date('Y-m-d H:i:s');
            $user->save();

            return back()->with('success', __('updated_successfully'));
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function profileUpdate(UserUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $request->id = Sentinel::getUser()->id;

            if($this->users->updateProfile($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function getBalanceInfo(Request $request)
    {
        $balance  = number_format($this->bank_accounts->bankRemainingBalance(@$request->table_name, @$request->id, @$request->row_id, @$request->purpose), 2);
        return response()->json($balance);
    }

    public function getAccounts(Request $request)
    {
        return response()->json($this->bank_accounts->accountsByUser($request->id));
    }

    public function logoutOtherDevices()
    {
        if (isDemoMode()) {
            $success[0] = __('this_function_is_disabled_in_demo_server');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
        try {
            $user = Sentinel::getUser();
            if(Sentinel::logout(null, true)):
                Sentinel::authenticate($user);
                $success[0] = __('logout_successfully');
                $success[1] = 'success';
                $success[2] = __('logout');
                return response()->json($success);
            endif;
        } catch (\Exception $e){
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }

    public function userAccounts()
    {
        $accounts = Sentinel::getUser()->accounts(Sentinel::getUser()->id);
        return view('common.profile.staff.accounts', compact('accounts'));
    }

    public function cacheClear()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('optimize:clear');
            cache()->flush();
            Toastr::success(__('cache_cleared_successfully'));

            return back();
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Error!');

            return back();
        }
    }


}
