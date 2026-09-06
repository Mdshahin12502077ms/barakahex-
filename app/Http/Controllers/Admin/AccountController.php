<?php

namespace App\Http\Controllers\Admin;

use App\Models\Parcel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Admin\IncomeDataTable;
use App\Repositories\Interfaces\ParcelInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Repositories\Interfaces\AccountInterface;
use App\Http\Requests\Admin\Account\CreditRequest;
use App\Repositories\Interfaces\DeliveryManInterface;
use App\Repositories\Interfaces\Admin\BankAccountInterface;
use App\Http\Requests\Admin\Account\CompanyAccountStoreRequest;
use Brian2694\Toastr\Facades\Toastr;

class AccountController extends Controller
{
    protected $company_accounts;
    protected $parcels;
    protected $delivery_man;
    protected $accounts;

    public function __construct(AccountInterface $company_accounts,ParcelInterface $parcels,DeliveryManInterface $delivery_man,BankAccountInterface $accounts)
    {
        $this->company_accounts = $company_accounts;
        $this->parcels          = $parcels;
        $this->delivery_man     = $delivery_man;
        $this->accounts         = $accounts;
    }

    public function index(IncomeDataTable $dataTable, Request $request)
    {

        $company_accounts = $this->company_accounts->paginate(\Config::get('parcel.paginate'));
        return $dataTable
            ->render('admin.accounts.index', compact('company_accounts'));
    }

    public function create()
    {
        $accounts         = $this->accounts->all()->where('user_id', Sentinel::getUser()->id);
        return view('admin.accounts.create', compact( 'accounts'));
    }

    public function store(CompanyAccountStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->company_accounts->store($request)):
                return redirect()->route('incomes')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }
    public function edit($id)
    {
        $company_account  = $this->company_accounts->get($id);
        $accounts         = $this->accounts->all()->where('user_id', Sentinel::getUser()->id);
        $delivery_man     = $this->delivery_man->get($company_account->delivery_man_id);
        $delivery_man_current_balnace = $delivery_man->balance($company_account->delivery_man_id);
        $current_amount   = __('current_balance').': '.format_price($company_account->amount + $delivery_man_current_balnace);
        return view('admin.accounts.edit', compact('company_account', 'accounts','current_amount'));
    }

    public function update(CompanyAccountStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->company_accounts->update($request)):
                return redirect()->route('incomes')->with('success', __('updated_successfully'));
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
            if($this->company_accounts->delete($id)):
                $success[0] = __('deleted_successfully');
                $success[1] = 'success';
                $success[2] = __('deleted');
                return response()->json($success);
            endif;
        } catch (\Exception $e){
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }

    public function creditCreate()
    {
        $parcels          = $this->parcels->all();
        $accounts         = $this->accounts->all()->where('user_id', Sentinel::getUser()->id);
        return view('admin.accounts.credit.create', compact('parcels', 'accounts'));
    }

    public function creditStore(CreditRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->company_accounts->creditStore($request)):
                return redirect()->route('incomes')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function merchantParcels(Request $request)
    {
        $parcels = Parcel::where('merchant_id', $request->merchant_id)->when(!hasPermission('read_all_parcel'), function ($query){
            $query->where(function ($q){
                $q->where('branch_id', Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
            });
        })->get();

        return view('admin.accounts.credit.parcel-options', compact('parcels'))->render();
    }

    public function creditEdit($id)
    {
        $company_account    = $this->company_accounts->get($id);

        if ($company_account->create_type=='user_defined' && ($company_account->merchantAccount->source == "cash_given_for_delivery_charge" && ($company_account->merchantAccount->payment_withdraw_id == null || $company_account->merchantAccount->is_paid == false))):
            $accounts           = $this->accounts->all()->where('user_id', Sentinel::getUser()->id);
            return view('admin.accounts.credit.edit', compact('company_account',  'accounts'));
        else:
            return back()->with('danger', __('you_are_not_allowed_to_update_this_anymore'));
        endif;

    }

    public function creditUpdate(CreditRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->company_accounts->creditUpdate($request)):
                return redirect()->route('incomes')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function balance(Request $request)
    {
        $delivery_man    = $this->delivery_man->get($request->id);
        $data = [];
        if ($request->data_for == 'update'):
            $company_account  = $this->company_accounts->get($request->company_account_id);
            if ($request->id == $company_account->delivery_man_id):
                $delivery_man     = $this->delivery_man->get($company_account->delivery_man_id);
                $delivery_man_current_balnace = $delivery_man->balance($company_account->delivery_man_id);
                $data['balance']   = __('current_balance').': '.format_price($company_account->amount + $delivery_man_current_balnace);
            else:
                $data['balance']   = __('current_balance').': '.format_price($delivery_man->balance($delivery_man->id));
            endif;
        else:
            $data['balance']       = __('current_balance').': '.format_price($delivery_man->balance($delivery_man->id));
        endif;
        return response()->json($data);
    }
}
