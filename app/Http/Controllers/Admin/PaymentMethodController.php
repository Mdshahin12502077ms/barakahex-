<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\Admin\PaymentMethodInterface;
use App\Traits\RepoResponseTrait;
use App\DataTables\Admin\PaymentMethodDataTable;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Admin\PaymentMethod\PaymentMethodStoreRequest;
use App\Http\Requests\Admin\PaymentMethod\PaymentMethodUpdateRequest;

class PaymentMethodController extends Controller
{
    use RepoResponseTrait;
    protected $payment;

    public function __construct(PaymentMethodInterface $payment)
    {
        $this->payment          = $payment;
    }
    public function index(Request $request, PaymentMethodDataTable $dataTable)
    {
        $payments        = $this->payment->paginate();

        return $dataTable->render('admin.payment-method.index', compact('payments'));

    }

    public function create()
    {
        return view('admin.payment-method.create');
    }

    public function store(PaymentMethodStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->payment->store($request)):
                return redirect()->route('admin.payment.method')->with('success', __('created_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function edit($id)
    {
        $payment = $this->payment->get($id);
        if (!blank($payment)):
            return view('admin.payment-method.edit', compact('payment'));
        else:
            return back()->with('danger', __('not_found'));
        endif;
    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>404,
                'message'=>$message,
            ]);
        }
        try {
            $status = $this->payment->statusChange($request);

            if($status == true){
                $success = __('updated_successfully');
                return response()->json([
                    'status'        =>200,
                    'message'       =>$success,
                ]);
            }
        } catch (\Exception $e){
            $message            = __('something_went_wrong_please_try_again');
            return response()->json([
                'status'        =>200,
                'message'       =>$message,
            ]);
        }


    }

    public function update(PaymentMethodUpdateRequest $request, $id)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->payment->update($request, $id)):
                return redirect()->route('admin.payment.method')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
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
            if($this->payment->delete($id)):
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
