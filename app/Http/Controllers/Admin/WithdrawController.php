<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\NotificationUser;
use App\Models\Account\MerchantWithdraw;
use App\Models\Parcel;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\MerchantPaymentAccount;
use App\Models\WithdrawBatch;
use App\Traits\CommonHelperTrait;
use App\Traits\MerchantBalanceTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\DataTables\Admin\PaymentDataTable;
use App\Repositories\Interfaces\WithdrawInterface;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\Repositories\Interfaces\Admin\WithdrawInterface as AdminWithdrawInterface;
use App\Http\Requests\Admin\Withdraw\WithdrawStoreRequest;
use App\Http\Requests\Admin\Withdraw\WithdrawUpdateRequest;
use App\Repositories\Interfaces\Admin\BankAccountInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\SendNotification;
class WithdrawController extends Controller
{
    use MerchantBalanceTrait, CommonHelperTrait, SendNotification;
    protected $withdraws;
    protected $admin_withdraws;
    protected $merchants;
    protected $bank_accounts;

    public function __construct(WithdrawInterface $withdraws, AdminWithdrawInterface $admin_withdraws, MerchantInterface $merchants, BankAccountInterface $bank_accounts)
    {
        $this->withdraws = $withdraws;
        $this->admin_withdraws = $admin_withdraws;
        $this->merchants = $merchants;
        $this->bank_accounts = $bank_accounts;
    }

    public function index(PaymentDataTable $dataTable, Request $request, $id = null)
    {
        //dd($request->all());
        $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);
        $withdraws = $this->withdraws->paginate(\Config::get('parcel.paginate'));
        $batches = WithdrawBatch::all();
        return $dataTable
            ->render('admin.withdraws.index', compact('withdraws', 'accounts', 'batches'));
    }

    public function create()
    {
        if (@settingHelper('preferences')->where('title', 'create_payment_request')->first()->staff):
            $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);
            return view('admin.withdraws.create', compact('accounts'));
        else:
            return back()->with('danger', __('service_unavailable'));
        endif;
    }

    public function getMerchantInfo(Request $request)
    {
        $merchant = $this->merchants->get($request->id);
        $payment_accounts = $merchant->paymentAccount;
        $data = $this->merchantBalance($merchant->id);
        $balance = $data['current_payable'];
        $payable_parcels = $data['parcels'];
        $payable_merchant_accounts = $data['merchant_accounts'];
        $payment_account = [];
        $payment_account = MerchantPaymentAccount::where('merchant_id', $merchant->id)
            ->with('paymentAccount')->whereHas('paymentAccount', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        if (isset($request->amount)) {
            $balance = $balance + $request->amount;
        }
        $data['balance'] = number_format($balance, 2, '.', '');


        $parcels = '';
        $merchant_accounts = '';

        foreach ($payable_parcels as $parcel):
            $parcels .= "<input type='hidden' value=\"$parcel->id\" name=\"parcels[]\" >";
        endforeach;

        foreach ($payable_merchant_accounts as $merchant_account):
            $merchant_accounts .= "<input type='hidden' value=\"$merchant_account->id\" name=\"merchant_accounts[]\" >";
        endforeach;
        $options = ' ';

        foreach ($payment_account as $method) {
            $options .= "<option value=\"$method->id\">" . __(@$method->paymentAccount->name) . "</option>";
        }

        $data['options'] = $options;
        $data['parcels'] = $parcels;
        $data['merchant_accounts'] = $merchant_accounts;

        return response()->json($data);
    }

    public function store(WithdrawStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($request->withdraw_to == 'cash') {
                $payment_accounts = $this->merchants->get($request->merchant);

            }
            $payment_accounts = $this->merchants->get($request->merchant)->paymentAccount;

            if (!$this->checkRoutingNo($payment_accounts->routing_no, $request->withdraw_to)):
                if (@settingHelper('preferences')->where('title', 'create_payment_request')->first()->staff):
                    if (isset($request->status) && $request->status == 'processed') {
                        $balance = $this->bank_accounts->bankRemainingBalance('', $request->account, '', '');
                        if ($balance < $request->amount) {
                            return back()->with('danger', __('the_account_has_no_available_balance'));
                        }
                    }
                    $data = $this->merchantBalance($request->merchant);

                    $current_payable = $data['current_payable'];

                    if (number_format($current_payable, 2, '.', '') != number_format($request->amount, 2, '.', '')):
                        return back()->with('danger', __('incorrect_amount_please_try_again'));
                    else:
                        if ($this->withdraws->store($request)):
                            return redirect()->route('admin.withdraws')->with('success', __('created_successfully'));
                        else:
                            return back()->with('danger', __('something_went_wrong_please_try_again'));
                        endif;
                    endif;
                else:
                    return redirect()->route('admin.withdraws')->with('danger', __('service_unavailable'));
                endif;
            else:
                return back()->with('danger', __('please_add_routing_no_to_your_bank_account'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        if ($id <= 1939):
            return back()->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
        endif;

        $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);

        $withdraw = $this->withdraws->get($id);
        $merchant_detail = $this->merchants->get($withdraw->merchant->id);

        $payment_accounts = $merchant_detail->paymentAccount;

        $payment_account = [];

        if ($payment_accounts->selected_bank != '' && $payment_accounts->bank_branch != '' && $payment_accounts->bank_ac_name != '' && $payment_accounts->bank_ac_number != ''):
            $payment_account[] = 'bank';
        endif;

        if ($payment_accounts->bkash_number != '' && $payment_accounts->bkash_ac_type != ""):
            $payment_account[] = 'bKash';
        endif;

        if ($payment_accounts->rocket_number != '' && $payment_accounts->rocket_ac_type != ""):
            $payment_account[] = 'rocket';
        endif;

        if ($payment_accounts->nogod_number != '' && $payment_accounts->nogod_ac_type != ""):
            $payment_account[] = 'nogod';
        endif;

        return view('admin.withdraws.edit', compact('withdraw', 'payment_account', 'merchant_detail', 'accounts'));
    }

    public function update(WithdrawUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($request->id <= 1939):
                return back()->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
            endif;

            $merchant_withdraw = MerchantWithdraw::find($request->id);

            $payment_accounts = $this->merchants->get($request->merchant)->paymentAccount;

            if (!$this->checkRoutingNo($payment_accounts->routing_no, $request->withdraw_to)):
                if (!$merchant_withdraw->withdrawBatch):
                    if (isset($request->status) && $request->status == 'processed') {

                        $balance = $this->bank_accounts->bankRemainingBalance('', $request->account, '', '');
                        if ($balance < $request->amount) {
                            return back()->with('danger', __('the_account_has_no_available_balance'));
                        }
                    }

                    $current_payable = $this->withdrawUpdateMerchantBalance($request->id);

                    if (number_format($current_payable, 2, '.', '') != number_format($request->amount, 2, '.', '')):
                        return back()->with('danger', __('incorrect_amount_please_try_again'));
                    else:
                        if ($this->withdraws->update($request)):
                            return redirect()->route('admin.withdraws')->with('success', __('updated_successfully'));
                        else:
                            return back()->with('danger', __('something_went_wrong_please_try_again'));
                        endif;
                    endif;
                else:
                    return redirect()->route('admin.withdraws')->with('danger', __('already_added_to_batch'));
                endif;
            else:
                return back()->with('danger', __('please_add_routing_no_to_your_bank_account'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }


    public function chargeStatus($id, $status)
    {
        if ($id <= 1808):
            $success[0] = __('you_are_not_allowed_to_reject_this_withdraw');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        endif;

        if ($this->admin_withdraws->chargeStatus($id, $status, '')):
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

    }

    public function processPayment(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $merchant_withdraw = MerchantWithdraw::find($request->id);

            if (!$merchant_withdraw->withdrawBatch):
                $balance = $this->bank_accounts->bankRemainingBalance('', $request->account, '', '');
                if ($balance < $merchant_withdraw->amount) {
                    return back()->with('danger', __('the_account_has_no_available_balance'));
                }

                $status = 'processed';

                if ($this->admin_withdraws->chargeStatus($request->id, $status, $request)):
                    $merchant_withdraw->withdraw_batch_id = null;
                    $merchant_withdraw->save();
                    return back()->with('success', __('processed_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('already_added_to_batch'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function approvePayment(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->admin_withdraws->chargeStatus($request->id, 'approved', $request)):
                return back()->with('success', __('approved_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function updateBatch(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->admin_withdraws->updateBatch($request->id, $request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function rejectPayment(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $status = 'rejected';
            if ($request->id <= 1939):
                return back()->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
            endif;
            if ($this->admin_withdraws->chargeStatus($request->id, $status, $request)):
                return back()->with('success', __('rejected_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }


    public function details($id)
    {
        $accounts = Account::all();

        $merchants = $this->merchants->all();

        $withdraw = $this->withdraws->get($id);
        $merchant_detail = $this->merchants->get($withdraw->merchant->id);

        $payment_accounts = MerchantPaymentAccount::where('merchant_id', $withdraw->merchant->id)->with('paymentAccount')->first();


        return view('admin.withdraws.details', compact('withdraw', 'merchants', 'payment_account', 'merchant_detail', 'accounts'));
    }


    public function invoice($id)
    {
        $withdraw = $this->withdraws->get($id);

        return view('admin.withdraws.invoice', compact('withdraw'));
    }


    public function filterByMerchantName(Request $request)
    {
        $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);

        $query = MerchantWithdraw::query();

        if ($request->merchant != "") {
            $query->where('merchant_id', $request->merchant);
        }
        if ($request->status != "") {
            $query->where('status', $request->status);
        }

        $withdraws = $query->latest()->paginate(\Config::get('parcel.paginate'));

        return view('admin.withdraws.index', compact('withdraws', 'accounts'));
    }

    public function print($id)
    {
        $withdraw = $this->withdraws->get($id);

        return view('admin.withdraws.print', compact('withdraw'));
    }

    public function filter(Request $request)
    {
        $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);
        $query = MerchantWithdraw::query();

        if ($request->withdraw_id != ""):
            $query->where('withdraw_id', $request->withdraw_id);
            $withdraws = $query->paginate(\Config::get('parcel.paginate'));

            if (blank($withdraws)) {
                return redirect()->route('admin.withdraws')->with('danger', __('no_record_found'));
            }

            return view('admin.withdraws.index', compact('withdraws', 'accounts'));
        endif;

    }


    public function remove($id)
    {
        if (isDemoMode()) {
            $success = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 500,
                'message' => $success,
            ]);
        }
        try {
            $withdraw = $this->withdraws->get($id);

            $withdraw->withdraw_batch_id = null;
            $withdraw->save();

            $success[0] = __('deleted_successfully');
            $success[1] = 'success';
            $success[2] = __('deleted');
            return response()->json($success);
        } catch (\Exception $e) {
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }
}
