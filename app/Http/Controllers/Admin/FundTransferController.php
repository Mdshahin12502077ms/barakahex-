<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Admin\FundTransferInterface;
use App\Repositories\Interfaces\Admin\BankAccountInterface;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\FundTransfer\FundTransferStoreRequest;
use App\DataTables\Admin\FundTransferDataTable;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Account\CompanyAccount;

class FundTransferController extends Controller
{
    protected $fund_transfers;
    protected $bank_accounts;
    protected $users;

    public function __construct(FundTransferInterface $fund_transfers, BankAccountInterface $bank_accounts, UserInterface $users)
    {
        $this->fund_transfers = $fund_transfers;
        $this->bank_accounts  = $bank_accounts;
        $this->users          = $users;
    }
    public function index(FundTransferDataTable $dataTable, Request $request)
    {
        $fund_transfers = $this->fund_transfers->all();
        return $dataTable->render('admin.accounts.fund-transfer.index', compact('fund_transfers'));
    }

    public function create()
    {
        $bank_accounts  = $this->bank_accounts->all();
        return view('admin.accounts.fund-transfer.create', compact('bank_accounts'));
    }

    public function store(FundTransferStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $from_account = $this->bank_accounts->get($request->from_account);

            $balance = $from_account->incomes()->sum('amount') + $from_account->fundReceives()->sum('amount') - $from_account->expenses()->sum('amount') - $from_account->fundTransfers()->sum('amount');

            if($balance < $request->amount){
                return back()->with('danger', __('the_account_has_no_available_balance'));
            }

            if($this->fund_transfers->store($request)):
                return redirect()->route('admin.fund-transfer')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        $bank_accounts  = $this->bank_accounts->all();
        $fund_transfer  = $this->fund_transfers->get($id);

        $balance  = number_format($this->bank_accounts->bankRemainingBalance('fund_transfers', '', @$id, 'edit'), 2);

        return view('admin.accounts.fund-transfer.edit', compact('bank_accounts', 'fund_transfer', 'balance'));
    }

    public function update(FundTransferStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $balance  = $this->bank_accounts->bankRemainingBalance('fund_transfers', @$request->from_account, @$request->id, 'update');

            if($balance < $request->amount){
                return back()->with('danger', __('the_account_has_no_available_balance'));
            }

            if($this->fund_transfers->update($request)):
                return redirect()->route('admin.fund-transfer')->with('success', __('updated_successfully'));
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
            if($this->fund_transfers->delete($id)):
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
}
