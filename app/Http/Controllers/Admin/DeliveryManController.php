<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\DeliveryManInterface;
use App\Http\Requests\Admin\DeliveryMan\DeliveryManStoreRequest;
use App\Http\Requests\Admin\DeliveryMan\DeliveryManUpdateRequest;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\RepoResponseTrait;
use App\Models\DeliveryMan;
use App\Models\LogActivity;
use Brian2694\Toastr\Facades\Toastr;
use App\DataTables\Admin\DeliveryManDataTable;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class DeliveryManController extends Controller
{
    use ApiReturnFormatTrait, RepoResponseTrait;
    protected $delivery_man;

    public function __construct(DeliveryManInterface $delivery_man)
    {
        $this->delivery_man     = $delivery_man;
    }

    public function index(DeliveryManDataTable $dataTable, Request $request)
    {
        $branchs         = Branch::all();
        $delivery_men = $this->delivery_man->paginate(\Config::get('parcel.paginate'));

        return $dataTable
            ->with(['request' => $request,  'delivery_men' => $delivery_men, 'branchs' => $branchs])
            ->render('admin.delivery-man.index', compact('delivery_men', 'branchs'));
    }

    public function create()
    {
        $branchs = Branch::all();
        return view('admin.delivery-man.create',compact('branchs'));
    }

    public function store(DeliveryManStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->delivery_man->store($request)):
                return redirect()->route('delivery.man')->with('success', __('created_successfully'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        $delivery_man = DeliveryMan::with('companyAccount')->find($id);
        if(hasPermission('read_all_delivery_man') || $delivery_man->user->branch_id == Sentinel::getUser()->branch_id || $delivery_man->user->branch_id == ''):
            $branchs = Branch::all();
            return view('admin.delivery-man.edit', compact('delivery_man','branchs'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function update(DeliveryManUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->delivery_man->update($request)):
                return redirect()->route('delivery.man')->with('success', __('updated_successfully'));
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
        try{
        $delivery_man = $this->delivery_man->get($id);
        if(hasPermission('read_all_delivery_man') || $delivery_man->user->branch_id == Sentinel::getUser()->branch_id || $delivery_man->user->branch_id == ''):
            if($this->delivery_man->delete($id)):
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
        endif;
        } catch (\Exception $e){
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }


    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>400,
                'message'=>$message,
            ]);
        }
        try {

            $status = $this->delivery_man->statusChange($request);
            if($status == true){
                $success = __('updated_successfully');
                return response()->json([
                    'status'=>200,
                    'message'=>$success,
                ]);
            }

        } catch (\Exception $e){
            $message = __('something_went_wrong_please_try_again');
            return response()->json([
                'status'=>400,
                'message'=>$message,
            ]);
        }

    }

    public function personalInfo($id)
    {
        $delivery_man = $this->delivery_man->get($id);
        if(hasPermission('read_all_delivery_man') || $delivery_man->user->branch_id == Sentinel::getUser()->branch_id || $delivery_man->user->branch_id == ''):
            return view('admin.delivery-man.details.personal-info', compact('delivery_man'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function accountActivity($id)
    {
        $delivery_man = $this->delivery_man->get($id);
        if(hasPermission('read_all_delivery_man') || $delivery_man->user->branch_id == Sentinel::getUser()->branch_id || $delivery_man->user->branch_id == ''):
            $login_activities = LogActivity::where('user_id', $delivery_man->user_id)->orderBy('id', 'desc')->paginate(5);
            return view('admin.delivery-man.details.account-activity', compact('login_activities', 'delivery_man'));
        else:
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function filter(Request $request)
    {
        $branchs = Branch::all();
        $delivery_men = $this->delivery_man->filter($request);
        return view('admin.delivery-man.index', compact('delivery_men', 'branchs'));

    }

    public function statements($id)
    {
        $delivery_man    = $this->delivery_man->get($id);
        $statements      = $delivery_man->accountStatements()->paginate(5);
        return view('admin.delivery-man.details.statements', compact('statements','delivery_man'));
    }
}
