<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Charge;
use App\Enums\StatusEnum;
use App\Models\CodCharge;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Enums\PaymentMethodType;
use App\Traits\RepoResponseTrait;
use App\Http\Requests\ShopRequest;
use App\Http\Controllers\Controller;
use App\Traits\ApiReturnFormatTrait;
use App\Models\MerchantPaymentAccount;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\UserInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Http\Requests\Merchant\OthersAccountRequest;
use App\DataTables\Admin\MerchantDatatable;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Admin\Merchant\MerchantStoreRequest;
use App\Http\Requests\Admin\Merchant\MerchantUpdateRequest;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\DataTables\Admin\MerchantShopDataTable;
use App\DataTables\Admin\MerchantStatementDataTable;
use Illuminate\Support\Facades\Log;

class MerchantController extends Controller
{
    use ApiReturnFormatTrait, RepoResponseTrait;
    protected $merchants;
    protected $users;

    public function __construct(MerchantInterface $merchants, UserInterface $users)
    {
        $this->merchants     = $merchants;
        $this->users         = $users;
    }

    public function index(MerchantDatatable $dataTable, Request $request)
    {
        $merchants                         = $this->merchants->paginate(\Config::get('parcel.parcel_merchant_paginate'));
        $branchs                           = Branch::all();
        $merchant_limit                    = env('ACTIVE_MERCHANT');
        $current_merchant_count            = 0;
        $current_merchant_count            = Merchant::where('status', 1)->where('created_at', '>=', now()->startOfMonth())->count();


        return $dataTable
            ->with(['request'   => $request, 'merchants' => $merchants, 'branchs' => $branchs])
            ->render('admin.merchants.index', compact('merchants', 'branchs', 'merchant_limit', 'current_merchant_count'));
    }


    public function create()
    {
        $cod_charges        = CodCharge::all();
        $charges            = Charge::all();
        $branchs            = Branch::all();
        return view('admin.merchants.create', compact('charges', 'cod_charges', 'branchs'));
    }


    public function store(MerchantStoreRequest $request)
    {
        
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        
        
        try {
            $result = $this->merchants->store($request);
            if ($result->status):
                return redirect()->route($result->redirect_to)->with($result->redirect_class, $result->msg);
            else:
                return redirect()->back()->with($result->redirect_class, $result->msg)->withInput();
            endif;
        } catch (\Exception $e) {
            Log::error('Merchant Store Error ' . $e->getMessage());
            return back()->with('danger', $e->getMessage() ?? __('something_went_wrong_please_try_again'))->withInput();
        }
    }

    public function edit($id)
    {
        $merchant          = $this->merchants->get($id);

        $branch_match      = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') ||  $branch_match ||  $branch_match == ''):
            $user          = $merchant->user;
            $branchs       = Branch::active()->get();
            return view('admin.merchants.edit', compact('merchant', 'user', 'branchs'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function update(MerchantUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $result = $this->merchants->update($request);
            if ($result->status):
                return redirect()->route($result->redirect_to)->with($result->redirect_class, $result->msg);
            else:
                return redirect()->back()->with($result->redirect_class, $result->msg);
            endif;
        } catch (\Exception $e) {
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
            $merchant       = $this->merchants->get($id);
            $branch_match   = $merchant->withPermission($merchant->id)->get();

            if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
                $user_id        = $merchant->user_id;

                if ($this->merchants->delete($user_id, $merchant)):
                    $success[0] = __('deleted_successfully');
                    $success[1] = 'success';
                    $success[2] = __('deleted');
                    return response()->json($success);
                endif;
            endif;
        } catch (\Exception $e) {
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }

    public function filter(Request $request)
    {
        $merchants      = $this->merchants->filter($request);
        $branchs        = Branch::all();
        return view('admin.merchants.index', compact('merchants', 'branchs'));
    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 404,
                'message' => $message,
            ]);
        }
        try {
            $status                                     = $this->merchants->statusChange($request);

            if ($status == true) {
                $success                                = __('updated_successfully');
                return response()->json([
                    'status'  => 200,
                    'message' => $success,
                ]);
            }
        } catch (\Exception $e) {
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message, 404);
        }
    }

    public function personalInfo($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match):
            return view('admin.merchants.details.personal-info', compact('merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function permissions($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return view('admin.merchants.details.permissions', compact('merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function accountActivity($id)
    {
        $merchant               = $this->merchants->get($id);
        $branch_match           = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            $login_activities   = LogActivity::where('user_id', $merchant->user_id)->orderBy('id', 'desc')->paginate(9);
            return view('admin.merchants.details.account-activity', compact('login_activities', 'merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function charge($id)
    {
        $merchant               = $this->merchants->get($id);
        $branch_match           = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return view('admin.merchants.details.charge', compact('merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function codCharge($id)
    {
        $merchant               = $this->merchants->get($id);
        $branch_match           = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return view('admin.merchants.details.cod-charge', compact('merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function company($id)
    {
        $merchant               = $this->merchants->get($id);
        $branch_match           = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return view('admin.merchants.details.merchant-company', compact('merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function statements($id, MerchantStatementDataTable $dataTable)
    {
        $merchant               = $this->merchants->get($id);
        $branch_match           = $merchant->withPermission($merchant->id)->get();


        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            $statements = $merchant->accountStatements()->orderBy('id', 'desc')->paginate(9);

            return $dataTable->with('id', $id)->render('admin.merchants.details.statement.statements', compact('statements', 'merchant'));

        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }
    public function shops($id, MerchantShopDatatable $dataTable)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();

        $branchs        = Branch::active()->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            $shops      = $merchant->shops()->paginate(\Config::get('parcel.paginate'));
            return $dataTable->with('id', $id)->render('admin.merchants.details.shop.shops', compact('shops', 'branchs', 'merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function shopDelete($id)
    {
        if (isDemoMode()) {
            $success = __('this_function_is_disabled_in_demo_server');
            return response()->json($success);
        }

        try {
            $shop  = Shop::find($id);
            $shop->delete();

            $success = __('deleted_successfully');
            return response()->json($success);
        } catch (\Exception $e) {
            $message  = __('something_went_wrong_please_try_again');
            return response()->json($message);
        }
    }

    public function shopStore(ShopRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->merchants->shopStore($request)):
                return back()->with('success', __('added_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function paymentAccounts($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();


        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            $payment_account = $merchant->paymentAccount;
            $payments        = MerchantPaymentAccount::where('merchant_id', $merchant->id)->with('paymentAccount')->where('type', 'mfs')->get();
            $bank            = MerchantPaymentAccount::where('merchant_id', $merchant->id)->where('type', 'bank')->with('paymentAccount')->first();

            return view('admin.merchants.details.bank_account', compact('payment_account', 'merchant', 'payments', 'bank'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }


    public function paymentAccountEdit($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();
        $methods        = PaymentMethod::where('type', 'bank')->where('status', 'active')->pluck('name', 'id');



        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            if ($payment_account = $this->merchants->paymentAccount($id)):
                $payment_account = $this->merchants->paymentAccount($id);

                return view('admin.merchants.details.bank_account_edit', compact('payment_account', 'merchant', 'methods'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function paymentAccountUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->merchants->updateBankDetails($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function paymentAccountOthersEdit($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();
        $methods        = PaymentMethod::where('payment_methods.type', PaymentMethodType::MFS->value)
            ->select('payment_methods.*', 'merchant_payment_accounts.mfs_number', 'merchant_payment_accounts.mfs_ac_type')
            ->leftJoin('merchant_payment_accounts', function ($join) use ($merchant) {
                $join->on('payment_methods.id', '=', 'merchant_payment_accounts.payment_method_id')
                    ->where('merchant_payment_accounts.merchant_id', '=', $merchant->id);
            })
            ->with('merchantPaymentAccount')
            ->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return view('admin.merchants.details.others_account_edit', compact('merchant', 'methods'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function paymentOthersAccount($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();


        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            $payment_account = $merchant->paymentAccount;
            return view('admin.merchants.details.accounts', compact('payment_account', 'merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function paymentAccountOthersUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->merchants->updateOthersAccountDetails($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function changeDefault(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 500,
                'message' => $message,
            ]);
        }
        try {
            $status = $this->merchants->changeDefault($request);

            if ($status == true) {
                $success = __('updated_successfully');
                return response()->json([
                    'status' => 200,
                    'message' => $success,
                ]);
            }
        } catch (\Exception $e) {
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message, 404);
        }
    }

    public function shopEdit(Request $request)
    {

        $shop           = Shop::find($request->shop_id);
        $branchs        = Branch::active()->get();

        return view('merchant.profile.shop.shop-update', compact('shop', 'branchs'))->render();
    }

    public function shopUpdate(ShopRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->merchants->shopUpdate($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function apiCredentials($id)
    {
        $merchant       = $this->merchants->get($id);
        $branch_match   = $merchant->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return view('admin.merchants.details.api-credentials', compact('merchant'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function apiCredentialsUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $merchant       = $this->merchants->get($request->id);
            $branch_match   = $merchant->withPermission($merchant->id)->get();

            if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
                if ($this->merchants->apiCredentialsUpdate($request)):
                    return back()->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function permissionUpdate(Request $request, $id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $merchant       = $this->merchants->get($id);
            $branch_match   = $merchant->withPermission($merchant->id)->get();

            if (hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
                if ($this->merchants->permissionUpdate($request, $merchant)):
                    return back()->with('success', __('updated_successfully'));
                else:
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
}
