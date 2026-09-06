<?php

namespace App\Repositories\Admin;

use App\Models\WithdrawBatch;
use App\Models\User;
use App\Repositories\Interfaces\Admin\BulkWithdrawInterface;
use App\Repositories\Interfaces\Admin\WithdrawInterface;
use App\Traits\SendNotification;
use DB;

class BulkWithdrawRepository implements BulkWithdrawInterface {

    use SendNotification;
    protected $admin_withdraws;

    public function __construct(WithdrawInterface $admin_withdraws)
    {
        $this->admin_withdraws = $admin_withdraws;
    }

    public function all()
    {
        return WithdrawBatch::all();
    }

    public function paginate()
    {
        return WithdrawBatch::orderByDesc('id')->paginate(\Config::get('parcel.paginate'));
    }

    public function get($id)
    {
        return WithdrawBatch::with('withdraws.payments.paymentAccount')->find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $withdraw_batch                      = new WithdrawBatch();
            $withdraw_batch->title               = $request->title;
            $withdraw_batch->batch_no            = 'PDL'.rand(100000,999999);
            $withdraw_batch->batch_type          = $request->batch_type;
            $withdraw_batch->note                = $request->note;
            $withdraw_batch->user_id             = \Sentinel::getUser()->id;
            $withdraw_batch->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $withdraw_batch                      = $this->get($request->id);
            $withdraw_batch->title               = $request->title;
            if ($request->has('batch_type')):
                $withdraw_batch->batch_type          = $request->batch_type;
            endif;
            $withdraw_batch->note                = $request->note;
            $withdraw_batch->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function changeStatus($id, $status, $request)
    {
        DB::beginTransaction();
        try {
            $user                       = \Sentinel::getUser() ?? jwtUser();;
            $withdraw_batch             = $this->get($id);
            $withdraw_batch->status     = $status;
            $withdraw_batch->account_id = $request->account;
            $withdraw_batch->receipt    = $request->file('receipt') ? $this->admin_withdraws->fileUpload($request->file('receipt')) : '';

            $withdraw_batch->save();

            if ($status == 'processed'):
                foreach ($withdraw_batch->withdraws as $withdraw):
                    $data['account']        = $request->account;
                    $data['transaction_id'] = $withdraw_batch->batch_no;
                    $data['batch'] = true;
                    $this->admin_withdraws->chargeStatus($withdraw->id, $status, $data);
                endforeach;
            endif;

            $users                          = [];
            if($user->user_type == 'staff') {
                $details                    = 'Your payout has been updated';
                $users                      = User::where('merchant_id', $withdraw->merchant_id)
                                            ->where(function($query) {
                                                $query->where('user_type', 'merchant')
                                                    ->orWhere('user_type', 'merchant_staff');
                                            })
                                            ->orWhere(function($query) use ($withdraw) {
                                                $query->whereHas('merchant', function ($query) use ($withdraw) {
                                                    $query->where('id', $withdraw->merchant_id);
                                                });
                                            })
                                            ->get();

                $permissions                = ['manage_payment', 'all_parcel_payment'];
                $title                      = 'Your payout has been updated';
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
            DB::rollback();
            return false;
        }
    }


    public function delete($id)
    {
        DB::beginTransaction();
        try{
            WithdrawBatch::destroy($id);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
