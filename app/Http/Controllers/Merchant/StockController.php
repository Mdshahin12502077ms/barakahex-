<?php

namespace App\Http\Controllers\Merchant;

use App\DataTables\Merchant\ProductDataTable;
use App\DataTables\Merchant\StockDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\ProductRequest;
use App\Http\Requests\Merchant\StockRequest;
use App\Repositories\MerchantProductRepository;
use App\Repositories\MerchantWarehouseRepository;
use App\Repositories\StockRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{

    protected $productRepo;
    protected $merchantWarehouse;
    protected $stockRepo;

    public function __construct(MerchantProductRepository $productRepo, MerchantWarehouseRepository $merchantWarehouse, StockRepository $stockRepo)
    {
        $this->stockRepo     = $stockRepo;

        $this->productRepo     = $productRepo;

        $this->merchantWarehouse     = $merchantWarehouse;
    }
    public function index(StockDataTable $dataTable, $type)
    {
        $data = [
          'products'=>$this->productRepo->merchantProduct(),
          'warehouses'=>$this->merchantWarehouse->merchantWarehouse(),
            'type' => $type,
        ];
        return $dataTable->with($data)->render('merchant.stock.index',$data);
    }

    public function Store(StockRequest $request)
    {
        if (isDemoMode()) {
            return response()->json([
                'status' => false,
                'error' => __('this_function_is_disabled_in_demo_server')
            ], 403);
        }
        try {
            if ($this->stockRepo->store($request)) {
                return response()->json([
                    'success' => __('create_successful'),
                    'route'   => route('merchant.stock.list'),
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
            if($this->productRepo->statusChange($request)):
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
            if($this->productRepo->delete($id)):
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
