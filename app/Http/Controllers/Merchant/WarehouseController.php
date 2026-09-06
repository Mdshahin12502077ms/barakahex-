<?php

namespace App\Http\Controllers\Merchant;

use App\DataTables\Merchant\WarehouseDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\WarehouseStoreRequest;
use App\Repositories\MerchantWarehouseRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    protected $merchantWarehouse;

    public function __construct(MerchantWarehouseRepository $merchantWarehouse)
    {
        $this->merchantWarehouse           = $merchantWarehouse;
    }
    public function index(WarehouseDataTable $dataTable, Request $request)
    {
        return $dataTable->render('merchant.warehouse.index');
    }
    public function Store(WarehouseStoreRequest $request)
    {
        if (isDemoMode()) {
            return response()->json([
                'status' => false,
                'error' => __('this_function_is_disabled_in_demo_server')
            ], 403);
        }

        try {
            if ($this->merchantWarehouse->store($request)) {
                return response()->json([
                    'success' => __('create_successful'),
                    'route'   => route('merchant.warehouse'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'error'  => __('something_went_wrong_please_try_again')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ]);
        }
    }


    public function edit($id)
    {
        $warehouse    = $this->merchantWarehouse->get($id);
        return view('merchant.warehouse.edit', compact( 'warehouse'));
    }

    public function update(WarehouseStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->merchantWarehouse->update($request)):
                return redirect()->route('merchant.warehouse')->with('success', __('updated_successfully'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }


    public function statusChange(Request $request)
    {
        if (isDemoMode()) {

            $success  = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>500,
                'message'=>$success,
            ]);
        }
        try{
            if($this->merchantWarehouse->statusChange($request)):
                $success = __('updated_successfully');
                return response()->json([
                    'status'=>200,
                    'message'=>$success,
                ]);
            else:
                $success = __('something_went_wrong_please_try_again');
                return response()->json($success);
            endif;
        }catch (\Exception $e){
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
            if($this->merchantWarehouse->delete($id)):
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
