<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UserStoreRequest;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Models\LogActivity;
use App\Models\User;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use App\Repositories\Interfaces\MerchantStaffInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\DataTables\Admin\MerchantStaffDataTable;

class StaffController extends Controller
{
    protected $staffs;
    protected $merchants;

    public function __construct(MerchantStaffInterface $staffs, MerchantInterface $merchants)
    {
        $this->staffs       = $staffs;
        $this->merchants    = $merchants;

    }

    public function staffs($id, Request $request, MerchantStaffDataTable $dataTable)
    {
        $merchant   = $this->merchants->get($id);
        $staffs     = $merchant->staffs()->paginate(\Config::get('parcel.paginate'));

        return $dataTable->with(['id' => $id])->render('admin.merchants.details.staff.index', compact('staffs','merchant'));
    }


    public function staffCreate($id)
    {
        $merchant   = $this->merchants->get($id);

        return view('admin.merchants.details.staff.create', compact('merchant'));
    }

    public function staffStore(UserStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->staffs->store($request)):
                return redirect()->route('detail.merchant.staffs', $request->merchant)->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success,404);
        }
    }

    public function staffEdit($id)
    {
        $staff = $this->staffs->get($id);

        return view('admin.merchants.details.staff.edit', compact('staff'));
    }

    public function staffUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {

            if($this->staffs->update($request)):
                return redirect()->route('detail.merchant.staffs', $request->merchant)->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success,404);
        }
    }
    public function personalInfo($id)
    {
        $staff = $this->staffs->get($id);
        if($staff->user_type == 'merchant_staff'):
            return view('admin.merchants.details.staff.personal-info', compact('staff'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function accountActivity($id)
    {
        $staff         = $this->staffs->get($id);
        if($staff->user_type == 'merchant_staff'):
            $login_activities = LogActivity::where('user_id', $id)->orderBy('id', 'desc')->limit(20)->get();
            return view('admin.merchants.details.staff.account-activity', compact('login_activities', 'staff'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>500,
                'message'=>$message,
            ]);
        }
        try {

            $status = $this->staffs->statusChange($request);
            if($status == true){
                $success = __('updated_successfully');
                return response()->json([
                    'status'=>200,
                    'message'=>$success,
                ]);
            }

        }catch (\Exception $e){
            $message = __('something_went_wrong_please_try_again');
            return response()->json([
                'status'=>500,
                'message'=>$message,
            ]);
        }
    }
}
