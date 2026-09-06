<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\Admin\BankAccountInterface;
use App\Models\StaffAccount;
use App\DataTables\Admin\AccountDataTable;
use App\Repositories\Interfaces\UserInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Http\Requests\Admin\Account\AccountRequest;
use Brian2694\Toastr\Facades\Toastr;

class BankAccountController extends Controller
{
    protected $accounts;
    protected $users;

    public function __construct(UserInterface $users, BankAccountInterface $accounts)
    {
        $this->accounts = $accounts;
        $this->users    = $users;
    }

    public function index(AccountDataTable $dataTable, Request $request)
    {
        $accounts = $this->accounts->paginate();
        return $dataTable->render('admin.accounts.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $methods    = PaymentMethod::where('type', '!=', 'cash')->get();
        $banks      = PaymentMethod::where('type', 'bank')->get();
        $users      = $this->users->all()->where('user_type', 'staff');
        return view('admin.accounts.accounts.create', compact('users', 'methods', 'banks'));
    }

    public function store(AccountRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->accounts->store($request)):
                return redirect()->route('admin.account')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        $account  = $this->accounts->get($id);
        $users    = $this->users->all()->where('user_type', 'staff');
        $methods  = PaymentMethod::get();
        $banks    = PaymentMethod::where('type', 'bank')->get();
        return view('admin.accounts.accounts.edit', compact('account', 'users', 'methods', 'banks'));
    }

    public function update(AccountRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->accounts->update($request)):
                return redirect()->route('admin.account')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function delete($id)
    {
        if (isDemoMode()) {
            $success[0] = __('this_function_is_disabled_in_demo_server');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
        try {
            if($this->accounts->delete($id)):
                $success[0] = __('deleted_successfully');
                $success[1] = 'success';
                $success[2] = __('deleted');
                return response()->json($success);
            else:
                $success[0] = __('something_went_wrong_please_try_again');
                $success[1] = 'error';
                $success[2] = __('oops');
                return response()->json($success);
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function statement($id)
    {
        $accounts    = StaffAccount::with('fundTransfer')->where('account_id', $id)->orWhere('from_account_id', $id)->orWhere('to_account_id', $id)->orderBy('id', 'desc')->paginate(5);
        $account     = Account::find($id);
        $grand_total = $account->incomes()->sum('amount') + $account->fundReceives()->sum('amount') - $account->expenses()->sum('amount') - $account->fundTransfers()->sum('amount');
        return view('admin.accounts.accounts.statement', compact('accounts', 'grand_total'));
    }

    public function staffStatement($id)
    {
        $accounts    = StaffAccount::with('fundTransfer')->where('account_id', $id)->orWhere('from_account_id', $id)->orWhere('to_account_id', $id)->orderBy('id', 'desc')->paginate(5);
        $account     = Account::find($id);
        if (Sentinel::getUser()->id == $account->user_id):
            $grand_total = $account->incomes()->sum('amount') + $account->fundReceives()->sum('amount') - $account->expenses()->sum('amount') - $account->fundTransfers()->sum('amount');
            return view('admin.accounts.accounts.statement', compact('accounts', 'grand_total'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;

    }
}
