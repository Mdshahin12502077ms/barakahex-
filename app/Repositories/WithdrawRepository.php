<?php

namespace App\Repositories;

use Image;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Models\User;
use App\Models\StaffAccount;
use App\Models\PaymentMethod;
use App\Traits\SmsSenderTrait;
use App\Enums\PaymentMethodType;
use App\Traits\CommonHelperTrait;
use Illuminate\Support\Facades\DB;
use App\Models\WithdrawSmsTemplate;
use App\Models\Account\CompanyAccount;
use App\Models\MerchantPaymentAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\MerchantWithdraw;
use App\Repositories\Interfaces\WithdrawInterface;
use App\Repositories\Interfaces\Merchant\MerchantInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Traits\MerchantBalanceTrait;
use App\Traits\SendNotification;
use App\Traits\RepoResponseTrait;

class WithdrawRepository implements WithdrawInterface {
    use CommonHelperTrait;
    use SmsSenderTrait;
    use SendNotification;
    use RepoResponseTrait;
    use MerchantBalanceTrait;

    protected $merchants;

    public function __construct(MerchantInterface $merchants){
        $this->merchants       = $merchants;
    }

    public function all()
    {
        return MerchantWithdraw::all();
    }

    public function paginate($limit)
    {
        return MerchantWithdraw::orderBy('id', 'desc')->paginate($limit);
    }

    public function get($id)
    {
        return MerchantWithdraw::with('payments')->find($id);
    }

    public function get_account_details($merchant, $withdraw_to)
    {
        try{

            $payment_accounts = MerchantPaymentAccount::where('merchant_id', $merchant)->where('id', $withdraw_to)->first();

            if ($payment_accounts->type == PaymentMethodType::BANK->value):
                $data['pay_by']          = __($payment_accounts->paymentAccount->name) . ', ' . $payment_accounts->bank_branch . ', ' . $payment_accounts->bank_ac_name . ', ' . $payment_accounts->bank_ac_number;
                $data['account_details'] = [__($payment_accounts->paymentAccount->name), $payment_accounts->bank_branch, $payment_accounts->bank_ac_name, $payment_accounts->bank_ac_number, $payment_accounts->routing_no];
            elseif ($payment_accounts->type == PaymentMethodType::MFS->value):
                $data['pay_by']          = __($payment_accounts->paymentAccount->name) . ', ' . $payment_accounts->mfs_number . ', ' . __($payment_accounts->mfs_ac_type);
                $data['account_details'] = [__($payment_accounts->paymentAccount->name), $payment_accounts->mfs_number, __($payment_accounts->mfs_ac_type)];
            elseif ($payment_accounts->type == PaymentMethodType::CASH->value):
                $data['pay_by']          = __($payment_accounts->paymentAccount->name);
                $data['account_details'] = [__($payment_accounts->paymentAccount->name)];
            endif;

            return $data;

        } catch (\Exception $e) {
            dd($e->getMessage());

        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try{
            $user                              = Sentinel::getUser() ?? $request->user;
            $withdraw                          = new MerchantWithdraw();
            $withdraw->withdraw_id             = 'PDL'.rand(10000000,99999999);
            $withdraw->merchant_id             = $request->merchant;
            $withdraw->amount                  = (double)$request->amount;
            if(isset($request->status)):
                $withdraw->status              = $request->status;
            endif;
            $payment_method_type               = MerchantPaymentAccount::where('merchant_id', $request->merchant)->where('id', $request->withdraw_to)->with('paymentAccount')->first();

            if($request->withdraw_to == 'cash'){
                $withdraw->payment_method_type         = $request->withdraw_to;
            }
            if($request->withdraw_to != 'cash'){
                $account_pay_by                        = $this->get_account_details($request->merchant, $request->withdraw_to);
                $request['pay_by']                     = $account_pay_by['pay_by'];
                $account_details                       = json_encode(array_filter($account_pay_by['account_details']));
                $withdraw->account_details             = $account_details;
                $withdraw->withdraw_to                 = $request->withdraw_to;
                $withdraw->payment_method_type         = $payment_method_type->paymentAccount->type;
            }
            $withdraw->created_by          = Sentinel::getUser()->id ?? $request->user->id;
            $withdraw->note                = $request->details;
            $withdraw->date                = date('Y-m-d');
            $withdraw->save();


            $total_payable = 0;
            if (isset($request->parcels)):
                $parcels = Parcel::whereIn('id', $request->parcels)->get();
                $total_payable += (double)$parcels->sum('payable');
                foreach ($parcels as $parcel):
                    $parcel->withdraw_id = $withdraw->id;
                    $parcel->is_paid = $request->status == 'processed' ? true : false;
                    $parcel->save();
                endforeach;
            endif;

            if (isset($request->merchant_accounts)):
                $merchant_accounts = MerchantAccount::whereIn('id', $request->merchant_accounts)->get();
                $income = (double)$merchant_accounts->where('type', 'income')->sum('amount');
                $expense = (double)$merchant_accounts->where('type', 'expense')->sum('amount');
                $total_payable += ($income - $expense);
                foreach ($merchant_accounts as $merchant_account):
                    $merchant_account->payment_withdraw_id = $withdraw->id;
                    $merchant_account->save();
                endforeach;
            endif;

            $remaining_balance = round($total_payable - (double)$withdraw->amount, 2);
            if ($remaining_balance > 0) {
                $remaining_account                       = new MerchantAccount();
                $remaining_account->source               = 'previous_balance';
                $remaining_account->type                 = 'income';
                $remaining_account->amount               = $remaining_balance;
                $remaining_account->merchant_id          = $withdraw->merchant_id;
                $remaining_account->merchant_withdraw_id = $withdraw->id;
                $remaining_account->details              = 'Remaining balance from payout #' . $withdraw->withdraw_id;
                $remaining_account->date                 = date('Y-m-d');
                $remaining_account->payment_withdraw_id  = null;
                $remaining_account->is_paid              = false;
                $remaining_account->save();
            }

            //company table data insertion and calculation
            $company_account                       = new CompanyAccount();
            $company_account->source               = 'payment_withdraw_by_merchant';
            $company_account->details              = $request->details ?? __('payment_withdraw_by_merchant');
            $company_account->date                 = date('Y-m-d');
            $company_account->amount               = $withdraw->amount;
            $company_account->created_by           = Sentinel::getUser()->id ?? $request->user->id;
            $company_account->merchant_id          = $withdraw->merchant_id;
            $company_account->merchant_withdraw_id = $withdraw->id;

            if($request->status == 'processed'):
                $company_account->transaction_id       = $request->transaction_id ? $request->transaction_id : '';
                if($request->account != ''):
                    $company_account->account_id       = $request->account;
                    $company_account->user_id          = $company_account->account->user->id;
                endif;
                $company_account->type                 = 'expense';
                $company_account->receipt              = $request->file('receipt') ? $this->fileUpload($request->file('receipt')) : '';
            endif;
            $company_account->save();

            if($request->account != '' && $request->status == 'processed'):
                //staff account calculation and insertion if processed
                $staff_account                      = new StaffAccount();
                $staff_account->source              = 'withdraw';
                $staff_account->details             = 'payment_withdraw_by_merchant';
                $staff_account->date                = date('Y-m-d');
                $staff_account->amount              = $withdraw->amount;
                $staff_account->user_id             = $company_account->user_id;
                $staff_account->account_id          = $request->account;
                $staff_account->company_account_id  = $company_account->id;
                $staff_account->save();
            endif;

            //merchant account insertion

            $merchant_account                       = new MerchantAccount();
            $merchant_account->source               = 'payment_withdraw_by_merchant';
            $merchant_account->merchant_withdraw_id = $withdraw->id;
            $merchant_account->details              = $request->details ?? __('payment_withdraw_by_merchant');
            $merchant_account->date                 = date('Y-m-d');
            $merchant_account->type                 = 'expense';
            $merchant_account->amount               = $withdraw->amount;
            $merchant_account->merchant_id          = $withdraw->merchant_id;
            $merchant_account->company_account_id   = $company_account->id;
            $merchant_account->save();


            // merchant sms start
            $sms_template = WithdrawSmsTemplate::where('subject','payment_create_event')->first();

            $sms_body               = str_replace('{account_details}', $request['pay_by'], $sms_template->content);
            $sms_body               = str_replace('{amount}', $withdraw->amount, $sms_body);
            $sms_body               = str_replace('{payment_id}', $withdraw->withdraw_id, $sms_body);
            if($company_account->receipt != '' || $company_account->transaction_id != ''):
                $sms_body           = str_replace('{our_company_name}', 'Transaction successfully processed.'.($company_account->receipt != '' ? 'Receipt: '.static_asset($company_account->receipt) :'') . ($company_account->transaction_id != '' ? ', Transaction ID: '.$company_account->transaction_id : ''). ' '.__('app_name') , $sms_body);
            else:
                $sms_body           = str_replace('{our_company_name}', __('app_name'), $sms_body);
            endif;
            $sms_body               = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);

            if($sms_template->sms_to_merchant):
                $this->test($sms_body, $withdraw->merchant->phone_number, 'payment_create_event', env('PROVIDER'), $sms_template->masking);
            endif;
            $status                         = $request->status == 1 ? 'approved' : 'rejected';

            $users                          = [];
            if ($user->user_type == 'merchant' || $user->user_type == 'merchant_staff') {
                $details                    = 'New payout request created' . '-' . (($user->merchant) ? $user->merchant->company : $user->staffMerchant->company);
                $users                      = User::where('user_type', 'staff')->get();
                $permissions                = ['withdraw_read', 'bulk_withdraw_read'];
                $title                      = 'New payout request created' . '-' . (($user->merchant) ? $user->merchant->company : $user->staffMerchant->company);
                $this->sendNotification($title, $users, $details, $permissions, 'success', url('admin/withdraw/invoice/' . $withdraw->id), '');
            } elseif ($user->user_type == 'staff') {

                $details                    = 'You have got a new payout';
                $users                      = User::where('merchant_id', $request->merchant)
                                            ->where(function($query) {
                                                $query->where('user_type', 'merchant')
                                                    ->orWhere('user_type', 'merchant_staff');
                                            })
                                            ->orWhere(function($query) use ($request) {
                                                $query->whereHas('merchant', function ($query) use ($request) {
                                                    $query->where('id', $request->merchant);
                                                });
                                            })
                                            ->get();

                $permissions                = ['manage_payment', 'all_parcel_payment'];

                $title                      = 'You have got a new payout';
                $merchantUsers              = $users->where('user_type', 'merchant');
                $staffUsers                 = $users->where('user_type', 'merchant_staff');
                if ($merchantUsers) {
                    $this->sendNotification($title, $merchantUsers, $details, $permissions, 'success', url('merchant/payment-invoice/' . $withdraw->id), '');
                }
                if($staffUsers){
                    $this->sendNotification($title, $staffUsers, $details, $permissions, 'success', url('staff/payment-invoice/' . $withdraw->id), '');
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

    public function update($request)
    {

        DB::beginTransaction();

        try{
            $withdraw                      = $this->get($request->id);
            if(isset($request->status)){
                $withdraw->status          = $request->status;
            }
            //sms purpose
            $account_pay_by             = $this->get_account_details($withdraw->merchant_id, $request->withdraw_to);
            $request['pay_by']          = $account_pay_by['pay_by'];
            $account_details            = json_encode(array_filter($account_pay_by['account_details']));

            $previous_account_details      = implode(',', json_decode($withdraw->account_details));
            $withdraw->account_details     = $account_details;
            $withdraw->withdraw_to         = $request->withdraw_to;
            $withdraw->note                = $request->details;
            $withdraw->save();

            foreach ($withdraw->parcels as $parcel):
                $parcel->is_paid = $request->status == 'processed' ? true : false;
                $parcel->save();
            endforeach;

            //company table data insertion and calculation
            $company_account                       = CompanyAccount::where('merchant_withdraw_id', $withdraw->id)->orderByDesc('id')->first();
            $company_account->details              = $request->details ?? __('payment_withdraw_by_merchant');
            $company_account->amount               = $withdraw->amount;

            if($request->status == 'processed'):
                $company_account->transaction_id       = $request->transaction_id ? $request->transaction_id : '';
                if($request->account != ''):
                    $company_account->account_id       = $request->account;
                    $company_account->user_id          = $company_account->account->user->id;
                endif;
                $company_account->receipt              = $request->file('receipt') ? $this->fileUpload($request->file('receipt')) : '';
            endif;

            $company_account->save();
            if($request->account != '' && $request->status == 'processed'):
                $staff_account                      = new StaffAccount();
                $staff_account->source              = 'withdraw';
                $staff_account->details             = 'payment_withdraw_by_merchant';
                $staff_account->date                = date('Y-m-d');
                $staff_account->type                = 'expense';
                $staff_account->amount              = $withdraw->amount;
                $staff_account->user_id             = $company_account->user_id;
                $staff_account->account_id          = $request->account;
                $staff_account->company_account_id  = $company_account->id;
                $staff_account->save();
            endif;

            $merchant_account                       = MerchantAccount::where('merchant_withdraw_id', $withdraw->id)->orderByDesc('id')->first();
            $merchant_account->details              = $request->details ?? __('payment_withdraw_by_merchant');
            $merchant_account->date                 = date('Y-m-d');
            $merchant_account->amount               = $withdraw->amount;
            $merchant_account->save();

            // merchant sms start
            $sms_template               = WithdrawSmsTemplate::where('subject','payment_update_event')->first();

            $sms_body                   = str_replace('{account_details}', $previous_account_details, $sms_template->content);
            $sms_body                   = str_replace('{new_account_details}', $request['pay_by'], $sms_body);
            $sms_body                   = str_replace('{amount}', $withdraw->amount, $sms_body);
            $sms_body                   = str_replace('{payment_id}', $withdraw->withdraw_id, $sms_body);
            if($company_account->receipt != '' || $company_account->transaction_id != ''):
                $sms_body               = str_replace('{our_company_name}', 'Transaction successfully processed.'.($company_account->receipt != '' ? 'Receipt: '.static_asset($company_account->receipt) :'') . ($company_account->transaction_id != '' ? ', Transaction ID: '.$company_account->transaction_id : ''). ' '.__('app_name') , $sms_body);
            else:
                $sms_body               = str_replace('{our_company_name}', __('app_name'), $sms_body);
            endif;
            $sms_body                   = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);

            if($sms_template->sms_to_merchant):
                $this->test($sms_body, $withdraw->merchant->phone_number, 'payment_update_event', env('PROVIDER'), $sms_template->masking);
            endif;
            //merchant sms end

            DB::commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try{
            $withdraw = MerchantWithdraw::find($id);
            if ($withdraw) {
                foreach ($withdraw->parcels as $parcel):
                    $parcel->is_paid = false;
                    $parcel->withdraw_id = null;
                    $parcel->save();
                endforeach;

                foreach ($withdraw->merchantAccounts as $merchant_accounts):
                    $merchant_accounts->payment_withdraw_id = null;
                    $merchant_accounts->save();
                endforeach;

                MerchantAccount::where('merchant_withdraw_id', $withdraw->id)
                    ->where('source', 'previous_balance')
                    ->delete();

                $withdraw->delete();
            }
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function statusChange($request)
    {
        $withdraw               = MerchantWithdraw::find($request['id']);
        $withdraw->status       = $request['status'];
        if($request['status'] == 'approved'):
            $withdraw->approved_by  = Sentinel::getUser()->id ?? $request->user->id;
        endif;

        if($request['status'] == 'processed'):
            $withdraw->paid_by      = Sentinel::getUser()->id ?? $request->user->id;
        endif;

        $result                 = $withdraw->save();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function updatePaymentMethod($request)
    {
        DB::beginTransaction();
        try{
            $user                           = Sentinel::getUser() ?? jwtUser();

            if($user){
                if($user->user_type == 'merchant'){
                    $request['merchant']    = Merchant::where('user_id', $user->id)->first();
                }
                if($user->user_type == 'merchant_staff'){
                    $request['merchant']    = Merchant::where('id', $user->merchant_id)->first();
                }
            }
            $merchant                       = Merchant::findOrFail($request->merchant->id);
            $merchant->withdraw             = $request->withdraw;
            $merchant->default_account_id   = $request->account_id;
            $merchant->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());

            DB::rollback();
            return false;
        }
    }



    public function updateBankDetails($data)
    {
        DB::beginTransaction();
        try{

            $user = jwtUser() ?? Sentinel::getUser();

            if ($user) {
                if ($user->user_type == 'merchant') {
                    $merchant = Merchant::where('user_id', $user->id)->first();
                } elseif ($user->user_type == 'merchant_staff') {
                    $merchant = Merchant::where('id', $user->merchant_id)->first();
                }
            }
                $payment_account = MerchantPaymentAccount::where('type',PaymentMethodType::BANK->value)->where('merchant_id', $merchant->id)
                    ->first();
                $type = PaymentMethod::where('id', $data->payment_method_id)->first();
                if (!$payment_account) {
                    $payment_account                    = new MerchantPaymentAccount;
                    $payment_account->merchant_id       = $merchant->id;
                    $payment_account->payment_method_id = $data->payment_method_id;
                }
                $payment_account->payment_method_id = $data->payment_method_id;
                $payment_account->selected_bank     = $type->name;
                $payment_account->bank_branch       = $data->bank_branch;
                $payment_account->bank_ac_name      = $data->bank_ac_name;
                $payment_account->bank_ac_number    = $data->bank_ac_number;
                $payment_account->routing_no        = $data->routing_no;
                $payment_account->type              = $type->type;
                $payment_account->save();
        //     }
        // }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }

    public function updateOthersAccount($data)
    {
        DB::beginTransaction();
        try {

            $user                 = jwtUser() ?? Sentinel::getUser();

            if ($user) {
                if ($user->user_type == 'merchant') {
                    $merchant     = Merchant::where('user_id', $user->id)->first();
                } elseif ($user->user_type == 'merchant_staff') {
                    $merchant    = Merchant::where('id', $user->merchant_id)->first();
                }
            }

            foreach ($data->mfs_number as $key => $number) {
                if (isset($data->payment_method_id[$key])) {
                    $payment_account = MerchantPaymentAccount::where('merchant_id', $merchant->id)
                        ->where('payment_method_id', $data->payment_method_id[$key])
                        ->first();

                    $type = PaymentMethod::where('id', $data->payment_method_id[$key])->first();

                    if (!$payment_account) {
                        $payment_account                    = new MerchantPaymentAccount;
                        $payment_account->merchant_id       = $merchant->id;
                        $payment_account->payment_method_id = $data->payment_method_id[$key];
                    }

                    $payment_account->mfs_number  = $number;
                    $payment_account->mfs_ac_type = $data->mfs_ac_type[$key];
                    $payment_account->type        = $type->type;
                    $payment_account->save();
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


    public function chargeStatus($id, $status)
    {

        DB::beginTransaction();
        try{

            $merchant_withdraw         = MerchantWithdraw::find($id);
            $previous_status           = $merchant_withdraw->status;
            $merchant_withdraw->status = $status;
            $merchant_withdraw->save();

            //company table refund
            $company_account                       = new CompanyAccount();
            $company_account->source               = 'withdraw_cancelled';
            $company_account->details              = 'payment_withdraw_request_cancelled_by_merchant';
            $company_account->date                 = date('Y-m-d');
            $company_account->type                 = 'income';
            $company_account->amount               = $merchant_withdraw->amount;
            $company_account->created_by           = \Sentinel::getUser()->id;
            $company_account->merchant_id          = $merchant_withdraw->merchant_id;
            $company_account->merchant_withdraw_id = $merchant_withdraw->id;
            $company_account->save();
            //refund end for company

            $merchant_account                       = new MerchantAccount();
            $merchant_account->source               = 'withdraw_cancelled';
            $merchant_account->merchant_withdraw_id = $merchant_withdraw->id;
            $merchant_account->details              = 'withdraw_request_cancelled';
            $merchant_account->date                 = date('Y-m-d');
            $merchant_account->type                 = 'income';
            $merchant_account->amount               = $merchant_withdraw->amount;
            $merchant_account->merchant_id          = $merchant_withdraw->merchant_id;
            $merchant_account->company_account_id   = $company_account->id;
            $merchant_account->save();
            //merchant refund end

            foreach ($merchant_withdraw->parcels as $parcel):
                $parcel->is_paid = false;
                $parcel->withdraw_id = null;
                $parcel->save();
            endforeach;

            foreach ($merchant_withdraw->merchantAccounts as $merchant_accounts):
                $merchant_accounts->payment_withdraw_id = null;
                $merchant_accounts->save();
            endforeach;

            MerchantAccount::where('merchant_withdraw_id', $merchant_withdraw->id)
                ->where('source', 'previous_balance')
                ->delete();

            // merchant sms start
            $sms_template = WithdrawSmsTemplate::where('subject','payment_cancelled_event')->first();

            $sms_body = str_replace('{account_details}', $merchant_withdraw->account_details, $sms_template->content);
            $sms_body = str_replace('{amount}', $merchant_withdraw->amount, $sms_body);
            $sms_body = str_replace('{our_company_name}', __('app_name'), $sms_body);
            $sms_body = str_replace('{current_date_time}', date('M d, Y h:i a'), $sms_body);
            $sms_body = str_replace('{payment_id}', $merchant_withdraw->withdraw_id, $sms_body);

            if($sms_template->sms_to_merchant):
                $this->test($sms_body, $merchant_withdraw->merchant->phone_number, 'payment_cancelled_event', env('PROVIDER'), $sms_template->masking);
            endif;
            //merchant sms end

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function fileUpload($image){

        $requestImage           = $image;
        $fileType               = $requestImage->getClientOriginalExtension();

        $original   = date('YmdHis') .'-receipt' . rand(1, 50) . '.' . $fileType;

        $directory              = 'admin/images/';

        if(!is_dir($directory)) {
            mkdir($directory);
        }

        $originalFileUrl       = $directory . $original;

        if($fileType == 'pdf'):
            $requestImage->move($directory,$original);
        else:
            Image::make($requestImage)->save($originalFileUrl, 80);
        endif;

        return $originalFileUrl;
    }

    public function removeOldFile($image)
    {
        if($image != "" && file_exists($image)):
            unlink($image);
        endif;
    }

    public function generate_random_string($length=10) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function paymentRequest($merchants)
    {
        \Log::error(123);
        foreach ($merchants as $merchant) {

            $data                   = $this->merchantBalance($merchant->id);
            $current_payable        = $data['current_payable'];
            $parcels                = $data['parcels'];
            $merchant_accounts      = $data['merchant_accounts'];
                if ($current_payable > 0) {
                    $withdraw                   = new MerchantWithdraw();
                    $withdraw->withdraw_id      = 'PDL' . rand(10000000, 99999999);
                    $withdraw->merchant_id      = $merchant->id;
                    $withdraw->amount           = $current_payable;
                    $withdraw->status           = "pending";

                    $payment_method_type = MerchantPaymentAccount::where('merchant_id', $merchant->id)
                        ->where('id', $merchant->default_account_id)
                        ->with('paymentAccount')
                        ->first();

                    if ($payment_method_type && $payment_method_type->paymentAccount->type == 'cash') {
                        $withdraw->payment_method_type  = $payment_method_type->paymentAccount->name;
                    } else {
                        $account_pay_by                 = $this->get_account_details($merchant->id, $merchant->default_account_id);
                        $withdraw->account_details      = json_encode(array_filter($account_pay_by['account_details']));
                        $withdraw->withdraw_to          = $merchant->default_account_id;
                        $withdraw->payment_method_type  = $payment_method_type->paymentAccount->type;
                    }

                    $withdraw->date                     = date('Y-m-d');

                    $withdraw->save();

                    if ($parcels) {
                        foreach ($parcels as $parcel) {
                            $parcel->withdraw_id        = $withdraw->id;
                            $parcel->is_paid            = 'pending';
                            $parcel->save();
                        }
                    }

                    if ($merchant_accounts) {
                        foreach ($merchant_accounts as $merchant_account) {
                            $merchant_account->payment_withdraw_id = $withdraw->id;

                            $merchant_account->save();
                        }
                    }

                    $company_account                        = new CompanyAccount();
                    $company_account->source                = 'payment_withdraw_by_merchant';
                    $company_account->date                  = date('Y-m-d');
                    $company_account->amount                = $withdraw->amount;
                    $company_account->merchant_id           = $withdraw->merchant_id;
                    $company_account->merchant_withdraw_id  = $withdraw->id;
                    $company_account->save();

                    $merchant_account                       = new MerchantAccount();
                    $merchant_account->source               = 'payment_withdraw_by_merchant';
                    $merchant_account->merchant_withdraw_id = $withdraw->id;
                    $merchant_account->date                 = date('Y-m-d');
                    $merchant_account->amount               = $withdraw->amount;
                    $merchant_account->merchant_id          = $withdraw->merchant_id;
                    $merchant_account->company_account_id   = $company_account->id;
                    $merchant_account->save();
                }
            }

    }
}
