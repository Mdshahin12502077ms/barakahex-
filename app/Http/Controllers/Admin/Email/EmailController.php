<?php

namespace App\Http\Controllers\Admin\Email;

use Exception;
use Illuminate\Http\Request;
use App\Traits\SendMailTrait;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Artisan;
use App\Repositories\EmailTemplateRepository;
use App\Http\Requests\Admin\EmailConfigureRequest;

class EmailController extends Controller
{
    use SendMailTrait;

    protected $settings;

    protected $emailTemplate;

    public function __construct(SettingRepository $settings, EmailTemplateRepository $emailTemplate)
    {
        $this->settings      = $settings;
        $this->emailTemplate = $emailTemplate;
    }

    public function serverConfiguration()
    {
        $mail_driver = setting('mail_driver');
        $data        = [
            'mail_driver' => $mail_driver,
        ];

        return view('admin.email-setup.server-configuration', $data);
    }

    public function emailTemplate(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $data = [
                'email_templates' => $this->emailTemplate->authentication(),
            ];

            return view('admin.email-setup.email-template', $data);
        } catch (Exception $e) {
            return back()->with('danger', $e->getMessage());
        }
    }

    public function serverConfigurationUpdate(EmailConfigureRequest $request): \Illuminate\Http\RedirectResponse
    {
        if (isDemoMode()) {
            return back()->with('danger', __('this_function_is_disabled_in_demo_server'));
        }
        $driver = $request->mail_server;
        if ($this->settings->update($request)) {

            if ($driver == 'smtp' || $driver == 'sendgrid' || $driver == 'mailgun' || $driver == 'sendinBlue' || $driver == 'zohoSMTP') {
                $mail_host            = setting('smtp_server_address');
                $mail_username        = setting('smtp_user_name');
                $mail_port            = setting('smtp_mail_port');
                $mail_address         = setting('mail_from_address');
                $name                 = setting('smtp_mail_from_name');
                $mail_password        = setting('smtp_password');
                $mail_encryption_type = setting('smtp_encryption_type');
            } elseif ($request->mail_server == 'sendmail') {
                $sendmail_path = setting('sendmail_path');
            }

            if ($request->mail_server == 'sendmail') {
                envWrite('MAIL_MAILER', 'sendmail');
                envWrite('MAIL_HOST', '');
                envWrite('MAIL_PORT', '');
                envWrite('MAIL_USERNAME', '');
                envWrite('MAIL_PASSWORD', '');
                envWrite('MAIL_ENCRYPTION', '');
                envWrite('MAIL_FROM_ADDRESS', '');
                envWrite('MAIL_FROM_NAME', '');
                envWrite('SENDMAIL_PATH', $sendmail_path);
            } else {
                envWrite('MAIL_MAILER', 'smtp');
                envWrite('MAIL_HOST', $request->smtp_server_address);
                envWrite('MAIL_PORT', $request->smtp_mail_port);
                envWrite('MAIL_USERNAME', $request->smtp_user_name);
                envWrite('MAIL_PASSWORD', $request->smtp_password);
                envWrite('MAIL_ENCRYPTION', $request->smtp_encryption_type);
                envWrite('MAIL_FROM_ADDRESS', $request->mail_from_address);
                envWrite('MAIL_FROM_NAME', $request->smtp_mail_from_name);
            }

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            return redirect()->back()->with('success', __('Setting Updated Successfully'));
        } else {

            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function sendTestMail(Request $request)
    {
        if (isDemoMode()) {
            return back()->with('danger', __('this_function_is_disabled_in_demo_server'));
        }
        $request->validate([
            'send_to' => 'required|email',
        ]);
        $send_to = $request->send_to;

        try {
            $data['content'] = __('Email is working Perfectly!! This is just a test email');
            $data['subject'] = __('Test Email');
            $this->sendmail($send_to, 'emails.auth.email-template', $data);
            return redirect()->back()->with('success', __('successfully_email_send'));
        } catch (Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }
    }

    public function templateBody(Request $request)
    {
        $templateBody = $this->emailTemplate->get($request->id);

        return response()->json($templateBody);
    }

    public function emailTemplateUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            return response()->json([
                'error' => __('this_function_is_disabled_in_demo_server'),
            ]);
        }

        $request->validate([
            'subject' => 'required',
            'body'    => 'required',
        ]);
        try {
            $this->emailTemplate->update($request->all());

            return response()->json([
                'success' => __('message_sent_successfully'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
