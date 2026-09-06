<?php

namespace App\Repositories\Admin;

use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Merchant;
use App\Models\StaffAccount;
use App\Models\User;
use App\Models\WithdrawSmsTemplate;
use App\Repositories\Interfaces\Admin\WithdrawInterface;
use App\Traits\CommonHelperTrait;
use App\Traits\ImageTrait;
use App\Traits\SendNotification;
use App\Traits\SmsSenderTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Image;
use SoapClient;

class WithdrawRepository implements WithdrawInterface
{
    use CommonHelperTrait;
    use SmsSenderTrait;
    use ImageTrait;
    use SendNotification;

    public function all()
    {
        return MerchantWithdraw::all();
    }

    public function chargeStatus($id, $status, $request = '')
    {
        DB::beginTransaction();
        try {
            $user                              = Sentinel::getUser() ?? jwtUser();;

            if ($status == "processed"):

                $merchant_withdraw          = MerchantWithdraw::find($id);
                $previous_amount            = $merchant_withdraw->amount;
                $merchant_withdraw->status  = $status;
                $previous_account_details   = $merchant_withdraw->account_details;
                $merchant_withdraw->save();

                foreach ($merchant_withdraw->parcels as $parcel):
                    $parcel->is_paid        = true;
                    $parcel->save();
                endforeach;

                $company_account                    = CompanyAccount::where('merchant_withdraw_id', $merchant_withdraw->id)->orderByDesc('id')->first();
                $company_account->transaction_id    = $request['transaction_id'];

                // dd($request['receipt']);

                if(!$request['batch']):
                    // $company_account->receipt       = $request->file('receipt') ? $this->fileUpload($request->file('receipt')) : '';
                    if ($request['receipt']) {
                        $response                                       = $this->saveImage($request['receipt'] ,'image');
                        $company_account->receipt                       = $response['images'];
                    }
                endif;
                $company_account->account_id        = $request['account'];
                $company_account->user_id           = $company_account->account->user->id;
                $company_account->type              = 'expense';
                $company_account->save();
                // dd($company_account);

                $staff_account                      = new StaffAccount();
                $staff_account->source              = 'withdraw';
                $staff_account->details             = 'payment_withdraw_by_merchant';
                $staff_account->date                = date('Y-m-d');
                $staff_account->type                = 'expense';
                $staff_account->amount              = $merchant_withdraw->amount;
                $staff_account->user_id             = $company_account->user_id;
                $staff_account->account_id          = $request['account'];
                $staff_account->company_account_id  = $company_account->id;
                $staff_account->save();


                $sms_template                       = WithdrawSmsTemplate::where('subject', 'payment_processed_event')->first();
                $sms_body                           = str_replace('{account_details}', $previous_account_details, $sms_template->content);
                $sms_body                           = str_replace('{amount}', $previous_amount, $sms_body);
                $sms_body                           = str_replace('{payment_id}', $merchant_withdraw->withdraw_id, $sms_body);
                if ($company_account->receipt != '' || $company_account->transaction_id != ''):
                    $sms_body                       = str_replace('{our_company_name}', getFileLink('80X80', $company_account->receipt)  . ($company_account->transaction_id != '' ? ', Transaction ID: ' . $company_account->transaction_id : '') . ' ' . __('app_name'), $sms_body);
                elseif($request['batch'] == true):
                    $sms_body                       = str_replace('{our_company_name}', __('app_name'), $sms_body);
                else:
                    $sms_body                       = str_replace('{our_company_name}', __('if_you_do_not_receive_your_payment_within_next_banking_day_please_contact_to').' '.__('app_name'), $sms_body);
                endif;
                $sms_body                           = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);

                if ($sms_template->sms_to_merchant):
                    $this->test($sms_body, $merchant_withdraw->merchant->phone_number, 'payment_processed_event', env('PROVIDER'),  $sms_template->masking);
                endif;

            elseif ($status == 'approved'):
                $merchant_withdraw          = MerchantWithdraw::find($id);
                $merchant_withdraw->status  = $status;

                if($request != ''):
                   if(@$request->has('withdraw_batch') && $request->withdraw_batch != ''):
                       $merchant_withdraw->withdraw_batch_id = $request->withdraw_batch;
                   endif;
                endif;
                $merchant_withdraw->save();

            elseif ($status == "rejected"):
                $merchant_withdraw                      = MerchantWithdraw::find($id);
                $previous_status                        = $merchant_withdraw->status;
                $merchant_withdraw->status              = $status;
                $merchant_withdraw->withdraw_batch_id   = null;
                $merchant_withdraw->save();

                //parcels withdraw statuses data removing
                foreach ($merchant_withdraw->parcels as $parcel):
                    $parcel->is_paid        = false;
                    $parcel->withdraw_id    = null;
                    $parcel->save();
                endforeach;

                foreach ($merchant_withdraw->merchantAccounts as $merchant_accounts):
                    $merchant_accounts->payment_withdraw_id = null;
                    $merchant_accounts->save();
                endforeach;

                //company table data insertion and calculation
                $company_account                        = new CompanyAccount();
                $company_account->source                = 'withdraw_rejected';
                $company_account->details               = 'merchant_payment_withdraw_rejected';
                $company_account->date                  = date('Y-m-d');
                $company_account->type                  = 'income';
                $company_account->amount                = $merchant_withdraw->amount;
                $company_account->created_by            = \Sentinel::getUser()->id;
                $company_account->merchant_id           = $merchant_withdraw->merchant_id;
                $company_account->merchant_withdraw_id  = $merchant_withdraw->id;
                $company_account->reject_reason         = $request->reject_reason;

                //if previously processed get refund to that staff account
                if ($previous_status == 'processed'):
                    $previous_company_account_detail    = CompanyAccount::where('merchant_withdraw_id', $merchant_withdraw->id)->orderByDesc('id')->first();
                    $company_account->account_id        = $previous_company_account_detail->account_id;
                    $company_account->user_id           = $company_account->account->user->id;

                endif;

                $company_account->save();

                //staff account calculation for refunding
                if ($previous_status == 'processed'):

                    $staff_account                      = new StaffAccount();
                    $staff_account->source              = 'withdraw_rejected';
                    $staff_account->details             = 'merchant_payment_withdraw_rejected';
                    $staff_account->date                = date('Y-m-d');
                    $staff_account->type                = 'income';
                    $staff_account->amount              = $merchant_withdraw->amount;
                    $staff_account->user_id             = $company_account->user_id;
                    $staff_account->account_id          = $company_account->account_id;
                    $staff_account->company_account_id  = $company_account->id;
                    $staff_account->save();
                endif;
                //end refunding

                //default merchant account withdraw_amount as income
                $merchant_account                       = new MerchantAccount();
                $merchant_account->source               = 'withdraw_rejected';
                $merchant_account->merchant_withdraw_id = $merchant_withdraw->id;
                $merchant_account->details              = 'withdraw_request_rejected';
                $merchant_account->date                 = date('Y-m-d');
                $merchant_account->type                 = 'income';
                $merchant_account->amount               = $merchant_withdraw->amount;
                $merchant_account->merchant_id          = $merchant_withdraw->merchant_id;
                $merchant_account->company_account_id   = $company_account->id;
                $merchant_account->save();

                // merchant sms start
                $sms_template = WithdrawSmsTemplate::where('subject', 'payment_rejected_event')->first();

                $sms_body = str_replace('{account_details}', $merchant_withdraw->account_details, $sms_template->content);
                $sms_body = str_replace('{amount}', $merchant_withdraw->amount, $sms_body);
                $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
                $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
                $sms_body = str_replace('{reject_reason}', $company_account->reject_reason, $sms_body);
                $sms_body = str_replace('{payment_id}', $merchant_withdraw->withdraw_id, $sms_body);

                if ($sms_template->sms_to_merchant):
                    $this->test($sms_body, $merchant_withdraw->merchant->phone_number, 'payment_rejected_event', env('PROVIDER'), $sms_template->masking);
                endif;
                //merchant sms end

            endif;

            $users                          = [];
            if ($user->user_type == 'staff') {
                $details                    = 'Your payout has been updated';
                $users                      = User::where('merchant_id', $merchant_withdraw->merchant_id)
                                            ->where(function($query) {
                                                $query->where('user_type', 'merchant')
                                                    ->orWhere('user_type', 'merchant_staff');
                                            })
                                            ->orWhere(function($query) use ($merchant_withdraw) {
                                                $query->whereHas('merchant', function ($query) use ($merchant_withdraw) {
                                                    $query->where('id', $merchant_withdraw->merchant_id);
                                                });
                                            })
                                            ->get();

                $permissions                = ['manage_payment', 'all_parcel_payment'];
                $title                      = 'Your payout has been updated';
                $merchantUsers              = $users->where('user_type', 'merchant');
                $staffUsers                 = $users->where('user_type', 'merchant_staff');
                if ($merchantUsers) {
                    $this->sendNotification($title, $merchantUsers, $details, $permissions, 'success', url('merchant/payment-invoice/' . $merchant_withdraw->id), '');
                }
                if($staffUsers){
                    $this->sendNotification($title, $staffUsers, $details, $permissions, 'success', url('staff/payment-invoice/' . $merchant_withdraw->id), '');
                }

            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }

    public function updateBatch($id, $request)
    {
        try {
            $user                              = Sentinel::getUser() ?? jwtUser();;
            $merchant_withdraw = MerchantWithdraw::find($id);
            if($request->withdraw_batch != ''):
                $merchant_withdraw->withdraw_batch_id = $request->withdraw_batch;
            else:
                $merchant_withdraw->withdraw_batch_id = null;
            endif;
            $merchant_withdraw->save();

            $users                          = [];
            if($user->user_type == 'staff') {
                $details                    = 'Your payout has been updated';
                $users                      = User::where('merchant_id', $merchant_withdraw->merchant_id)
                                            ->where(function($query) {
                                                $query->where('user_type', 'merchant')
                                                    ->orWhere('user_type', 'merchant_staff');
                                            })
                                            ->orWhere(function($query) use ($merchant_withdraw) {
                                                $query->whereHas('merchant', function ($query) use ($merchant_withdraw) {
                                                    $query->where('id', $merchant_withdraw->merchant_id);
                                                });
                                            })
                                            ->get();

                $permissions                = ['manage_payment', 'all_parcel_payment'];
                $title                      = 'Your payout has been updated';
                $merchantUsers              = $users->where('user_type', 'merchant');
                $staffUsers                 = $users->where('user_type', 'merchant_staff');
                if ($merchantUsers) {
                    $this->sendNotification($title, $merchantUsers, $details, $permissions, 'success', url('merchant/payment-invoice/' . $merchant_withdraw->id), '');
                }
                if($staffUsers){
                    $this->sendNotification($title, $staffUsers, $details, $permissions, 'success', url('staff/payment-invoice/' . $merchant_withdraw->id), '');
                }

            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }

    }

    public function fileUpload($image)
    {
        $requestImage       = $image;
        $fileType           = $requestImage->getClientOriginalExtension();
        $original           = date('YmdHis') . '-receipt' . rand(1, 50) . '.' . $fileType;
        $directory          = 'admin/images/';

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        $originalFileUrl = $directory . $original;

        if ($fileType == 'pdf'):
            $requestImage->move($directory, $original);
        else:
            Image::make($requestImage)->save($originalFileUrl, 80);
        endif;

        return $originalFileUrl;
    }

    public function removeOldFile($image)
    {
        if ($image != "" && file_exists($image)):
            unlink($image);
        endif;
    }
}
