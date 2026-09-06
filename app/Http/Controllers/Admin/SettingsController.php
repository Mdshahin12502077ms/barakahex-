<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Models\PackageAndCharge;
use Illuminate\Support\Facades\App;
use App\Models\Parcel;
use App\Models\Preference;
use App\Models\Charge;
use App\Models\CodCharge;
use App\Repositories\Interfaces\Admin\SettingInterface;
use App\Traits\RepoResponseTrait;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Timezone;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Admin\Charge\ChargeStoreRequest;
use App\Http\Requests\Admin\Charge\ChargeUpdateRequest;

class SettingsController extends Controller
{
    use RepoResponseTrait;
    protected $setting;
    public function __construct(SettingInterface $setting)
    {
        $this->setting    = $setting;

    }
    public function pagination()
    {
        return view('admin.settings.pagination');
    }
    public function charges()
    {
        $cod_charges    = CodCharge::all();
        $charges        = Charge::all();
        return view('admin.settings.charges', compact('cod_charges', 'charges'));
    }

    public function sms()
    {
        return view('admin.settings.sms-settings');
    }
    public function preference(Request $request)
    {
        $data = [
            'languages'  => Language::active()->pluck('name','locale'),
            'time_zones' => Timezone::select('timezone','id','gmt_offset')->get(),
            'lang'       => $request->site_lang ? $request->site_lang : App::getLocale(),
        ];
        return view('admin.settings.preference-settings', $data);
    }
    public function packingCharge()
    {
        $packaging_and_charges = $this->setting->packingCharge();
        if($packaging_and_charges != false):
            return view('admin.settings.packing-charges', compact('packaging_and_charges'));
        else:
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        endif;

    }
    public function chargeAdd()
    {
        $view = view('admin.settings.charge_new_row')->render();
        return response()->json(['view' => $view]);
    }


    public function packingChargeAdd()
    {
        $view = view('admin.settings.packaging_charge_new_row')->render();

        return response()->json(['view' => $view]);
    }



    public function chargeUpdate(ChargeStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->setting->chargeUpdate($request)):
                return back()->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }

    }

    public function databaseBackupSetting()
    {
        return view('admin.settings.database-backup');
    }
    public function store(SettingUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->setting->store($request)):
                if($request->ajax()){
                    $data['status']    = 200;
                    $data['message']   = __('updated_successfully');
                    $data['title']     = 'success';
                    return response()->json($data);
                }else{
                    return back()->with('success', __('updated_successfully'));

                }
            else:
                if($request->ajax()){
                    $data['error']    = false;
                    $data['message']    = __('something_went_wrong_please_try_again');
                    return response()->json($data);
                }else{
                    return back()->with('danger', __('something_went_wrong_please_try_again'));

                }
            endif;


        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function mobileAppSetting()
    {
        return view('admin.settings.mobile-app-setting');
    }

    public function packagingChargeUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            if($this->setting->packagingChargeUpdate($request)):
                return back()->with('success', __('updated_successfully'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function deletePackagingCharge(Request $request, $id)
    {
        if (isDemoMode()) {
            $data['error']       = true;
            $data['message']     = __('this_function_is_disabled_in_demo_server');
            return response()->json($data);
        }
        try{
            $parcels = Parcel::where('packaging', $id)->first();

            if (!blank($parcels)):
                $data['success']    = false;
                $data['message']    = __('this_packaging_already_got_used');
                return response()->json($data);
            endif;

            if($this->setting->deletePackagingCharge($id)):
                $data['success']     = true;
                $data['message']     = __('deleted_successfully');
                return response()->json($data);
            endif;
        } catch (\Exception $e){
            $error = __('something_went_wrong_please_try_again');
            return response()->json($error);
        }
    }

    public function statusChange(Request $request)
    {

        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'    =>404,
                'message'   =>$message,
                'title'     => 'error',
            ]);
        }
        try{

            $sms_template = Preference::find($request['data']['id']);
            $sms_template[$request['data']['change_for']] = $request['data']['status'];
            $sms_template->save();
            $data = [
                'status'  => 200,
                'message' => __('update_successful'),
                'title'   => 'success',
            ];

            return response()->json($data);
        }
        catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            $data = [
                'status'  => 201,
                'message' => $success,
                'title'   => 'error',
            ];
            return response()->json($data);
        }

    }



}
