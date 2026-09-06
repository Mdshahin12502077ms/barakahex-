<?php

namespace App\Http\Controllers\Merchant;

use App\DataTables\Merchant\ProductDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\ProductRequest;
use App\Repositories\MerchantProductRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    protected $productRepo;

    public function __construct(MerchantProductRepository $productRepo)
    {
        $this->productRepo     = $productRepo;
    }
    public function index(ProductDataTable $dataTable)
    {
        return $dataTable->render('merchant.product.index');
    }

    public function Store(ProductRequest $request)
    {
        if (isDemoMode()) {
            return response()->json([
                'status' => false,
                'error' => __('this_function_is_disabled_in_demo_server')
            ], 403);
        }
        try {
            if ($this->productRepo->store($request)) {
                return response()->json([
                    'success' => __('create_successful'),
                    'route'   => route('merchant.products'),
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
        $product    = $this->productRepo->get($id);
        return view('merchant.product.edit', compact( 'product'));
    }

    public function update(ProductRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->productRepo->update($request)):
                return redirect()->route('merchant.products')->with('success', __('updated_successfully'));
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
