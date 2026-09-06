<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerParcelSmsTemplates;
use App\Models\SmsTemplate;
use App\Models\WithdrawSmsTemplate;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use Database\Seeders\CustomerParcelSmsSeeder;
use App\Repositories\CountryRepository;
use App\Repositories\SettingRepository;
use App\Repositories\Interfaces\Admin\PreferenceInterface;
use Brian2694\Toastr\Facades\Toastr;
use App\Traits\SmsSenderTrait;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    use RepoResponseTrait, ApiReturnFormatTrait, SmsSenderTrait,  RepoResponseTrait;

    protected $sms;

    public function __construct(PreferenceInterface $sms)
    {
        $this->sms = $sms;
    }

    public function smsPreference()
    {
        $sms_templates          = SmsTemplate::all();
        $customer_sms_templates = CustomerParcelSmsTemplates::all();
        $withdraw_sms_templates = WithdrawSmsTemplate::all();
        return view('admin.preference.sms', compact('sms_templates','customer_sms_templates','withdraw_sms_templates'));
    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            $message        = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'    => 404,
                'message'   =>$message,
            ]);
        }

        try{
            if($request['data']['change_for'] == 'sms_to_merchant'):
                $sms_template                           = SmsTemplate::find($request['data']['id']);
                $sms_template->sms_to_merchant          = $request['data']['status'];
                $sms_template->save();
            elseif($request['data']['change_for'] == 'sms_to_customer'):
                $customer_sms_template                  = CustomerParcelSmsTemplates::find($request['data']['id']);
                $customer_sms_template->sms_to_customer = $request['data']['status'];
                $customer_sms_template->save();
            elseif($request['data']['change_for'] == 'withdraw_sms'):
                $withdraw_sms_template                  = WithdrawSmsTemplate::find($request['data']['id']);
                $withdraw_sms_template->sms_to_merchant = $request['data']['status'];
                $withdraw_sms_template->save();
            endif;


            $success = __('updated_successfully');
            return response()->json([
                'status'=>200,
                'message'=>$success,
            ]);
        }
        catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($request['data']['id']);
        }

    }

    public function maskingStatusChange(Request $request)
    {
        if (isDemoMode()) {
            $message = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status'=>404,
                'message'=>$message,
            ]);
        }
        try{
            if($request['data']['change_for'] == 'sms_to_merchant'):
                $sms_template                       = SmsTemplate::find($request['data']['id']);
                $sms_template->masking              = $request['data']['status'];
                $sms_template->save();
            elseif($request['data']['change_for'] == 'sms_to_customer'):
                $customer_sms_template              = CustomerParcelSmsTemplates::find($request['data']['id']);
                $customer_sms_template->masking     = $request['data']['status'];
                $customer_sms_template->save();
            elseif($request['data']['change_for'] == 'withdraw_sms'):
                $withdraw_sms_template              = WithdrawSmsTemplate::find($request['data']['id']);
                $withdraw_sms_template->masking     = $request['data']['status'];
                $withdraw_sms_template->save();
            endif;

            $success = __('updated_successfully');
            return response()->json([
                'status'=>200,
                'message'=>$success,
            ]);
        }
        catch (\Exception $e){
            $success = __('something_went_wrong_please_try_again');
            return response()->json($request['data']['id']);
        }

    }

    public function otpSetting(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.otp_setting.otp_setting');
    }

    public function saveOTP(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try{
            $request->validate([
                'twilio_sms_sid'          => 'required_if:active_sms_provider,==,twillio',
                'twilio_sms_auth_token'   => 'required_if:active_sms_provider,==,twillio',
                'valid_twilio_sms_number' => 'required_if:active_sms_provider,==,twillio',
                'fast_2_auth_key'         => 'required_if:active_sms_provider,==,fast2',
                'fast_2_entity_id'        => 'required_if:active_sms_provider,==,fast2',
                'fast_2_route'            => 'required_if:active_sms_provider,==,fast2',
                'fast_2_language'         => 'required_if:active_sms_provider,==,fast2',
                'fast_2_sender_id'        => 'required_if:active_sms_provider,==,fast2',
                'spagreen_sms_api_key'    => 'required_if:active_sms_provider,==,spagreen',
                'spagreen_secret_key'     => 'required_if:active_sms_provider,==,spagreen',
                'mimo_username'           => 'required_if:active_sms_provider,==,mimo',
                'mimo_sms_password'       => 'required_if:active_sms_provider,==,mimo',
                'mimo_sms_sender_id'      => 'required_if:active_sms_provider,==,mimo',
                'nexmo_sms_key'           => 'required_if:active_sms_provider,==,nexmo',
                'nexmo_sms_secret_key'    => 'required_if:active_sms_provider,==,nexmo',
                'ssl_sms_api_token'       => 'required_if:active_sms_provider,==,ssl_wireless',
                'ssl_sms_url'             => 'required_if:active_sms_provider,==,ssl_wireless',
            ]);


            $status = $this->sms->update($request);


            if($status == true):
                return back()->with('success', __('created_successfully'));
            endif;
        }catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function sendNumber(Request $request, CountryRepository $countryRepository): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'test_number' => 'required',
        ]);
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        try {
            $data         = $request->all();
            $data['code'] = $countryRepository->getCode($data['phone_country_id']);

            $this->test($data);

            return response()->json([
                'success' => __('message_sent_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function otpStatusChange(Request $request)
    {
        if (isDemoMode()) {
            $data['error']       = true;
            $data['message']     = __('this_function_is_disabled_in_demo_server');
            return response()->json($data);
        }

        try{


            $status = $this->sms->statusChange($request);

            if($status == true){
                $success = __('updated_successfully');
                return response()->json([
                    'status'    =>200,
                    'message'   =>$success,
                ]);
            }
        }catch (\Exception $e){
            $message = __('something_went_wrong_please_try_again');
            return response()->json([
                'status'    =>404,
                'message'   =>$message,
            ]);
        }
    }

    public function smsTemplates(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $data = [
                'sms_templates' => SmsTemplate::all(),
            ];

            return view('backend.admin.otp_setting.sms_templates', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function saveTemplate(Request $request, CountryRepository $countryRepository): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'body' => 'required',
        ]);

        if (setting('active_sms_provider') == 'fast2') {
            $request->validate([
                'template_id' => 'required',
            ]);
        }
        if (isDemoMode()) {
            $success  = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'success' => $success,
            ]);
        }

        try {

            $sms_template = SmsTemplate::where('key', $request->key)->first();

            if ($sms_template) {
                $sms_template->update([
                    'body'        => $request->body,
                    'template_id' => $request->template_id,
                ]);
            } else {
                SmsTemplate::create([
                    'key'         => $request->key,
                    'title'       => $request->title,
                    'body'        => $request->body,
                    'short_codes' => $request->short_codes,
                    'template_id' => $request->template_id,
                ]);
            }
            Toastr::success(__('update_successful'));

            $success = __('updated_successfully');
            return response()->json([
                'status'    =>200,
                'message'   =>$success,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
