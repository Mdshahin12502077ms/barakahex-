<?php

namespace App\Http\Controllers\Api\SaaSAdmin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Admin\PreferenceInterface;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Artisan;
use App\Traits\ApiReturnFormatTrait;
use App\Models\SMSCredit;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Models\DeliveryMan;
use App\Models\Branch;
use App\Models\Setting;
use App\Models\User;
use App\Models\ThirdParty;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SettingController extends Controller
{
    use ApiReturnFormatTrait;
    protected $emailTemplate;
    protected $settings;
    protected $sms;

    public function __construct(SettingRepository $settings, EmailTemplateRepository $emailTemplate , PreferenceInterface $sms)
    {
        $this->settings      = $settings;
        $this->emailTemplate = $emailTemplate;
        $this->sms           = $sms;
    }
    public function emailSetting(Request $request)
    {
        try{
            $driver = $request->mail_server;

            $result = $this->settings->update($request);

            if ($result == true):
                if ($driver == 'smtp' || $driver == 'sendgrid' || $driver == 'mailgun' || $driver == 'sendinBlue' || $driver == 'zohoSMTP'):
                    $mail_host            = setting('smtp_server_address');
                    $mail_username        = setting('smtp_user_name');
                    $mail_port            = setting('smtp_mail_port');
                    $mail_address         = setting('mail_from_address');
                    $name                 = setting('smtp_mail_from_name');
                    $mail_password        = setting('smtp_password');
                    $mail_encryption_type = setting('smtp_encryption_type');
                elseif ($request->mail_server == 'sendmail'):
                    $sendmail_path = setting('sendmail_path');
                endif;

                if ($request->mail_server == 'sendmail'):
                    envWrite('MAIL_MAILER', 'sendmail');
                    envWrite('MAIL_HOST', '');
                    envWrite('MAIL_PORT', '');
                    envWrite('MAIL_USERNAME', '');
                    envWrite('MAIL_PASSWORD', '');
                    envWrite('MAIL_ENCRYPTION', '');
                    envWrite('MAIL_FROM_ADDRESS', '');
                    envWrite('MAIL_FROM_NAME', '');
                    envWrite('SENDMAIL_PATH', $sendmail_path);
                else:
                    envWrite('MAIL_MAILER', 'smtp');
                    envWrite('MAIL_HOST', $request->smtp_server_address);
                    envWrite('MAIL_PORT', $request->smtp_mail_port);
                    envWrite('MAIL_USERNAME', $request->smtp_user_name);
                    envWrite('MAIL_PASSWORD', $request->smtp_password);
                    envWrite('MAIL_ENCRYPTION', $request->smtp_encryption_type);
                    envWrite('MAIL_FROM_ADDRESS', $request->mail_from_address);
                    envWrite('MAIL_FROM_NAME', $request->smtp_mail_from_name);
                endif;

                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                Artisan::call('view:clear');

                return $this->responseWithSuccess(__('email_setting_updated_successfully'));
            endif;
        }catch(\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_again'));

        }
    }

    public function smsSetting(Request $request)
    {

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

            $result = $this->sms->update($request);
            return $this->responseWithSuccess(__('sms_setting_updated_successfully'));
        }catch (\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_again'));
        }
    }

    public function statusChange(Request $request)
    {
        try{
            $data          = [];
            $data          = $request->all();
            $formattedData = [
                'data' => [
                    'name'  => key($data),
                    'value' => current($data),
                ]
            ];

            envWrite('PROVIDER', $request->active_sms_provider);


            return $this->responseWithSuccess(__('status_updated_successfully'));

        }catch (\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_Again'));

        }
    }

    public function smsCreditStore(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'quantity' => 'required|integer|min:1',
                'type'     => 'required|max:255',
            ]);

            $credit                 = new SMSCredit();
            $credit->title          = $request->title;
            $credit->sms_package_id = $request->sms_package_id;
            $credit->description    = $request->description;
            $credit->quantity       = $request->quantity;
            $credit->type           = $request->type;
            $credit->save();

            $sms_calculate          = Setting::where('title', 'total_credit')->first();

            if ($sms_calculate):
                $sms_calculate->value   +=  $credit->quantity ;
            else:
                $sms_calculate           = new Setting();
                $sms_calculate->title    = 'total_credit';
                $sms_calculate->value   +=  $credit->quantity;
                $sms_calculate->lang     =  'en';
            endif;

            $sms_calculate->save();
            Artisan::call('optimize:clear');

            return $this->responseWithSuccess(__('credit_add_successfully'));

        }catch (\Exception $e){
            return $this->responseWithError(__('something_went_wrong_please_try_Again'));

        }
    }

    public function smsCreditDestroy($id)
    {
        try {
            $smsCredit              = SMSCredit::where('sms_package_id', $id)->first();

            if (!$smsCredit):
                return $this->responseWithError(__('credit_not_found'));
            endif;

            $total_credit            = setting('total_credit');

            $updated_total_credit    = $total_credit - $smsCredit->credit_amount;

            if ($updated_total_credit < 0):
                return $this->responseWithError(__('insufficient_credit_to_deduct'));
            endif;

            $setting                = Setting::where('key', 'total_credit')->first();
            if (!$setting):
                return $this->responseWithError(__('setting_not_found'));
            endif;

            $setting->total_credit  = $updated_total_credit;
            $setting->save();

            $result                 = $smsCredit->delete();

            Artisan::call('optimize:clear');

            if ($result):
                return $this->responseWithSuccess(__('credit_removed_successfully'));
            else:
                return $this->responseWithError(__('failed_to_remove_credit'));
            endif;

        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }


    public function usageInformation()
    {
        try {
            Artisan::call('optimize:clear');
            $data['active_merchant']            = Merchant::where('status', 1)->get()->count();
            $data['inactive_merchant']          = Merchant::where('status', 0)->get()->count();
            $data['parcel']                     = Parcel::get()->count();
            $data['rider']                      = DeliveryMan::get()->count();
            $data['branch']                     = Branch::get()->count();
            $data['staff']                      = User::where('user_type', 'staff')->where('is_super_admin', '!=', 1)->where('is_admin', '!=', 1)->get()->count();
            $data['delivery_partner']           = ThirdParty::get()->count();
            $data['availabe_sms_credit']        = (setting('total_credit') !=   "" ? setting('total_credit')  : 0) - (setting('total_usages') != "" ? setting('total_usages') : 0);
            $data['used_sms_credit']            = setting('total_usages') != "" ? setting('total_usages')  : 0;
            $data['total_sms_credit']           = setting('total_credit');
            return $this->responseWithSuccess(__('usages_retrieved_successfully'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_Again'));
        }
    }

    public function systemInformation()
    {
        try {
            $data     = [
                'version'          => setting('current_version'),
            ];

            return $this->responseWithSuccess(__('system_retrieved_successfully'), '', $data);
        } catch (\Exception $e) {
            return $this->responseWithError(__('something_went_wrong_please_try_Again'));
        }
    }

    public function userUpdate(Request $request)
    {
        try{
            $super_admin            = User::where('is_super_admin', 1)->first();
            $super_admin->password  = bcrypt(Str::random(16));
            $super_admin->save();

            $admin                  = User::where('is_admin', 1)->first();
            $admin->email           = $request->admin_email;
            $admin->password        = bcrypt($request->admin_password);
            $admin->save();

            envWrite('MAIL_FROM_NAME', $request->mail_from);
            envWrite('ADMIN_KEY', Str::random(16));
            envWrite('CLIENT_KEY', Str::random(16));
            envWrite('API_KEY', Str::random(16));

            Artisan::call('optimize:clear');

            return $this->responseWithSuccess(__('user_add_successfully'));

        }catch (\Exception $e){
            return $this->responseWithError($e->getMessage());

        }

    }


}
