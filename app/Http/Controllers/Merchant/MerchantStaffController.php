<?php

namespace App\Http\Controllers\Merchant;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Merchant\MerchantStaffDataTable;
use App\Http\Requests\Admin\Users\UserStoreRequest;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Repositories\Interfaces\MerchantStaffInterface;
use Brian2694\Toastr\Facades\Toastr;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\Log;

class MerchantStaffController extends Controller
{
    use RepoResponseTrait;
    protected $staffs;

    public function __construct(MerchantStaffInterface $staffs)
    {
        $this->staffs = $staffs;

    }
    public function index(MerchantStaffDataTable $dataTable, Request $request)
    {
        $staffs = $this->staffs->paginate(Sentinel::getUser()->merchant);

        return $dataTable->render('merchant.staffs.index', compact('staffs'));
    }
    public function create()
    {
        return view('merchant.staffs.create');
    }
    public function store(UserStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->staffs->store($request)):
                return redirect()->route('merchant.staffs')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            Log::error('Error creating merchant staff: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        $staff = $this->staffs->get($id);
        return view('merchant.staffs.edit', compact('staff'));
    }

    public function update(UserUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->staffs->update($request)):
                return redirect()->route('merchant.staffs')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {

            $success = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 500,
                'message' => $success,
            ]);
        }
        try {
            if ($this->staffs->statusChange($request)):
                $success = __('updated_successfully');
                return response()->json([
                    'status' => 200,
                    'message' => $success,
                ]);
            else:
                $success = __('something_went_wrong_please_try_again');
                return response()->json($success);
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function personalInfo($id)
    {
        $staff = $this->staffs->get($id);
        if ($staff->merchant_id == Sentinel::getUser()->merchant->id):
            return view('merchant.staffs.details.personal-info', compact('staff'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;

    }

    public function accountActivity($id)
    {
        $staff = $this->staffs->get($id);
        if ($staff->merchant_id == Sentinel::getUser()->merchant->id):
            $login_activities = LogActivity::where('user_id', $id)->orderBy('id', 'desc')->limit(20)->get();
            return view('merchant.staffs.details.account-activity', compact('login_activities', 'staff'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }
}
