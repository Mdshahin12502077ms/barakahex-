<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Enums\PaymentMethodType;
use App\Traits\CommonHelperTrait;
use App\Http\Controllers\Controller;
use App\Traits\MerchantBalanceTrait;
use App\Models\MerchantPaymentAccount;
use Illuminate\Support\Facades\Config;
use App\Models\Account\MerchantWithdraw;
use App\Repositories\Interfaces\WithdrawInterface;
use App\Http\Requests\Merchant\OthersAccountRequest;
use App\Http\Requests\Merchant\WithdrawStoreRequest;
use App\DataTables\Merchant\MerchantPaymentDataTable;
use App\Http\Requests\Merchant\WithdrawUpdateRequest;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Http\Requests\Merchant\DefaultPaymentMethodRequest;
use App\Http\Requests\Merchant\BankAccountUpdateRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;


class WithdrawController extends Controller
{
    use MerchantBalanceTrait, CommonHelperTrait;

    protected $withdraws;

    public function __construct(WithdrawInterface $withdraws)
    {
        $this->withdraws = $withdraws;
    }

    public function index(MerchantPaymentDataTable $dataTable, Request $request)
    {
        $withdraws = Sentinel::getUser()->merchant->withdraws()->paginate(Config::get('parcel.paginate'));
        return $dataTable->render('merchant.withdraw.index', compact('withdraws'));
    }

    public function create()
    {
        if (@settingHelper('preferences')->where('title','create_payment_request')->first()->merchant):
            if (Sentinel::getUser()->user_type == 'merchant') {
                $merchant = Merchant::where('user_id', Sentinel::getUser()->id)->first();
            }
            if (Sentinel::getUser()->user_type == 'merchant_staff') {
                $merchant = Merchant::where('id', Sentinel::getUser()->merchant_id)->first();
            }
            $payment_account        = MerchantPaymentAccount::where('merchant_id', $merchant->id)->groupBy('payment_method_id')->with('paymentAccount')->get();
            $payment_accounts       = Sentinel::getUser()->merchant->paymentAccount;
            $data                   = $this->merchantBalance(Sentinel::getUser()->merchant->id);
            $current_payable        = $data['current_payable'];
            $parcels                = $data['parcels'];
            $merchant_accounts      = $data['merchant_accounts'];
        else:
            return redirect()->route('merchant.staff.withdraw')->with('danger', __('service_unavailable'));
        endif;


        return view('merchant.withdraw.create', compact('payment_account', 'current_payable', 'parcels', 'merchant_accounts'));
    }

    public function store(WithdrawStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            $payment_accounts = Sentinel::getUser()->merchant->paymentAccount;
            if (!$this->checkRoutingNo($payment_accounts->routing_no, $request->withdraw_to)) :
                if (@settingHelper('preferences')->where('title', 'create_payment_request')->first()->merchant) :
                    $data = $this->merchantBalance(Sentinel::getUser()->merchant->id);

                    $current_payable    = $data['current_payable'];

                    if (number_format($current_payable, 2, '.', '') != number_format($request->amount, 2, '.', '')) :
                        return back()->with('danger', __('incorrect_amount_please_try_again'));
                    else :
                        if ($this->withdraws->store($request)) :
                            return redirect()->route('merchant.withdraw')->with('success', __('created_successfully'));
                        else :
                            return back()->with('danger', __('something_went_wrong_please_try_again'));
                        endif;
                    endif;
                else :
                    return redirect()->route('merchant.withdraw')->with('danger', __('service_unavailable'));
                endif;
            else :
                return back()->with('danger', __('please_add_routing_no_to_your_bank_account'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
            if ($id <= 1939) :
                return back()->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
            endif;
            $payment_accounts = Sentinel::getUser()->merchant->paymentAccount;

            $withdraw = $this->withdraws->get($id);

            if ($withdraw->status == 'pending' && $withdraw->merchant->id == Sentinel::getUser()->merchant->id) :
                // $payment_account[] = 'cash';
                $payment_account = [];

                if ($payment_accounts->selected_bank != '' &&  $payment_accounts->bank_branch != '' && $payment_accounts->bank_ac_name != '' && $payment_accounts->bank_ac_number != '') :
                    $payment_account[] =  'bank';
                endif;

                if ($payment_accounts->bkash_number != '' &&  $payment_accounts->bkash_ac_type) :
                    $payment_account[] =  'bKash';
                endif;

                if ($payment_accounts->rocket_number != '' &&  $payment_accounts->rocket_ac_type) :
                    $payment_account[] =  'rocket';
                endif;

                if ($payment_accounts->nogod_number != '' &&  $payment_accounts->nogod_ac_type) :
                    $payment_account[] =  'nogod';;
                endif;

                return view('merchant.withdraw.edit', compact('withdraw', 'payment_account'));
            else :
                return back()->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
            endif;
    }

    public function update(WithdrawUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if ($request->id <= 1939) :
                return back()->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
            endif;
            $payment_accounts = Sentinel::getUser()->merchant->paymentAccount;

            if (!$this->checkRoutingNo($payment_accounts->routing_no, $request->withdraw_to)) :
                $merchant_withdraw                 = MerchantWithdraw::find($request->id);
                if (!$merchant_withdraw->withdrawBatch) :

                    $current_payable = $this->withdrawUpdateMerchantBalance($request->id);

                    if (number_format($current_payable, 2, '.', '') != number_format($request->amount, 2, '.', '')) :
                        return back()->with('danger', __('incorrect_amount_please_try_again'));
                    else :
                        if ($this->withdraws->update($request)) :
                            return redirect()->route('merchant.withdraw')->with('success', __('updated_successfully'));
                        else :
                            return back()->with('danger', __('something_went_wrong_please_try_again'));
                        endif;
                    endif;
                else :
                    return redirect()->route('merchant.withdraw')->with('danger', __('you_are_not_allowed_to_update_this_withdraw'));
                endif;
            else :
                return back()->with('danger', __('please_add_routing_no_to_your_bank_account'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function delete($id)
    {
        if ($this->withdraws->delete($id)) :
            $success = __('deleted_successfully');
            return response()->json($success);
        else :
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success);
        endif;
    }

    public function invoice($id)
    {
        $withdraw = $this->withdraws->get($id);

        if ($withdraw->merchant_id == Sentinel::getUser()->merchant->id) :
            return view('merchant.withdraw.invoice', compact('withdraw'));
        else :
            return back()->with('danger', __('unable_to_access'));
        endif;
    }

    public function invoicePrint($id)
    {
        $withdraw = $this->withdraws->get($id);

        if ($withdraw->merchant_id == Sentinel::getUser()->merchant->id) :
            return view('merchant.withdraw.print', compact('withdraw'));
        else :
            return back()->with('danger', __('unable_to_access'));
        endif;
    }

    //payment accounts functions

    public function paymentMethod()
    {
        if (Sentinel::getUser()->user_type == 'merchant') {
            $merchant = Merchant::where('user_id', Sentinel::getUser()->id)->first();
        }
        if (Sentinel::getUser()->user_type == 'merchant_staff') {
            $merchant = Merchant::where('id', Sentinel::getUser()->merchant_id)->first();
        }
        $data['merchant'] = $merchant;
        $data['accounts'] = MerchantPaymentAccount::with('paymentAccount')->groupBy('payment_method_id')->where('merchant_id', $merchant->id)->get();

        return view('merchant.payment.payment-method', $data);
    }

    public function updatePaymentMethod(DefaultPaymentMethodRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if ($this->withdraws->updatePaymentMethod($request)) :
                return redirect()->back()->with('success', __('updated_successfully'));
            else :
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function paymentAccounts()
    {

        $data['methods'] = PaymentMethod::with('payment')->where('type', 'bank')->where('status','ACTIVE')->get();

        if (Sentinel::getUser()->user_type == 'merchant') {
            $merchant = Merchant::where('user_id', Sentinel::getUser()->id)->first();
        }

        if (Sentinel::getUser()->user_type == 'merchant_staff') {
            $merchant = Merchant::where('id', Sentinel::getUser()->merchant_id)->first();
        }

        $payment = MerchantPaymentAccount::where('merchant_id', $merchant->id)->where('type', 'bank')->first();

        if ($payment) {
            $data['method'] = PaymentMethod::where('id', $payment->payment_method_id)->with('payment')->first();
        }

        return view('merchant.payment.bank_account', $data);
    }


    public function mfsAccounts()
    {

        $methods         = PaymentMethod::with('payment')->where('type', 'mfs')->where('status','ACTIVE')->get();

        return view('merchant.payment.mfs', compact('methods'));
    }

    public function paymentBankUpdate(BankAccountUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if ($this->withdraws->updateBankDetails($request)) :
                return redirect()->back()->with('success', __('updated_successfully'));
            else :
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function paymentOthersAccount()
    {
        $payment_account = Sentinel::getUser()->merchant->paymentAccount;
        return view('merchant.payment.others_account', compact('payment_account'));
    }

    public function paymentOthersAccountUpdate(OthersAccountRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if ($this->withdraws->updateOthersAccount($request)) :
                return redirect()->back()->with('success', __('updated_successfully'));
            else :
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function chargeStatus($id, $status)
    {
        $withdraw = $this->withdraws->get($id);

        if ($id <= 1939) :
            $success[0] = __('you_are_not_allowed_to_cancel_this_withdraw');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        endif;


        if ($withdraw->status == 'pending' && $withdraw->merchant->id == Sentinel::getUser()->merchant->id) :
            if ($this->withdraws->chargeStatus($id, $status)) :
                $success[0] = __('updated_successfully');
                $success[1] = 'success';
                $success[2] = __('updated');
                return response()->json($success);
            else :
                $success[0] = __('something_went_wrong_please_try_again');
                $success[1] = 'error';
                $success[2] = __('oops');
                return response()->json($success);
            endif;
        else :
            $success[0] = __('you_are_not_allowed_to_update_this_withdraw');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        endif;
    }
}
