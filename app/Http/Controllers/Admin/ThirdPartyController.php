<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThirdPartyRequest;
use App\Repositories\Interfaces\Admin\ThirdPartyInterface;
use App\DataTables\Admin\ThirdPartyDataTable;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ThirdPartyController extends Controller
{
    use RepoResponseTrait, ApiReturnFormatTrait;
    protected $third_parties;

    public function __construct(ThirdPartyInterface $third_parties)
    {
        $this->third_parties = $third_parties;
    }

    public function index(ThirdPartyDataTable $dataTable, Request $request)
    {
        $third_parties = $this->third_parties->paginate();

        return $dataTable->render('admin.third-parties.index', compact('third_parties'));
    }

    public function create()
    {
        return view('admin.third-parties.create');
    }

    public function edit($id)
    {
        $third_party    = $this->third_parties->get($id);

        return view('admin.third-parties.edit', compact( 'third_party'));
    }

    public function store(ThirdPartyRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->third_parties->store($request)):
                return redirect()->route('admin.third-parties')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success,404);
        }
    }

    public function update(ThirdPartyRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->third_parties->update($request)):
                return redirect()->route('admin.third-parties')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($success,404);
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
            if($this->third_parties->delete($id)):
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

    public function changeStatus(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>500,
                'message'=>$message,
            ]);
        }
        try {
            $status = $this->third_parties->changeStatus($request);
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
