<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ParcelsImport;
use App\Models\Shop;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Log;
use Maatwebsite\Excel\Validators\ValidationException;


class ImportExportController extends Controller
{
    public function importExportView()
    {
        $merchants = User::where('user_type', 'merchant')->get();
        return view('admin.bulk.import', compact('merchants'));
    }
    public function getShopsByMerchant(Request $request)
    {
        $shops = Shop::where('merchant_id', $request->merchant_id)->select('id', 'shop_name')
            ->get();
        return response()->json($shops);
    }

    public function export()
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $filename = (Sentinel::getUser()->user_type == 'merchant' || Sentinel::getUser()->user_type == 'merchant_staff') ? 'admin/excel/merchant-parcel-import-sample.xlsx' : 'admin/excel/staff-parcel-import-sample.xlsx';
            if (file_exists(public_path($filename))):
                $filepath = public_path($filename);
                return Response::download($filepath);
            else:
                return back()->with('danger', __('file_not_found'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function import()
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $extension = request()->file('file')->getClientOriginalExtension();
            if ($extension != 'xlsx' && $extension != 'csv'):
                return back()->with('danger', __('file_type_not_supported'));
            endif;
            $file = request()->file('file')->store('import');

            $import = new ParcelsImport();
            $import->import($file);
            unlink(storage_path('app/' . $file));

            return back()->with('success', __('successfully_imported'));
        } catch (ValidationException $e) {
            $failures = $e->failures();

            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            $errorMessage = implode('<br>', $messages);

            return back()->with('danger', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return back()->with('danger', $e->getMessage());
        }
    }
}
