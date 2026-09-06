<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\PercelOtpDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\OtpLogRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PercelOtpController extends Controller
{
    protected $repository;

    public function __construct(OtpLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(PercelOtpDataTable $dataTable)
    {
        return $dataTable->render('admin.percel_otp.index');
    }

    public function delete($id)
    {
        if (config('app.demo_mode')) {
            $response = [__('this_function_is_disabled_in_demo_server'), 'error', __('error')];
            return response()->json($response);
        }

        if ($this->repository->destroy($id)) {
            $response = [__('deleted_successfully'), 'success', __('success')];
            if (request()->ajax()) {
                return response()->json($response);
            }
            Toastr::success(__('deleted_successfully'));
        } else {
            $response = [__('something_went_wrong_please_try_again'), 'error', __('error')];
            if (request()->ajax()) {
                return response()->json($response);
            }
            Toastr::error(__('something_went_wrong_please_try_again'));
        }

        return redirect()->back();
    }
}
