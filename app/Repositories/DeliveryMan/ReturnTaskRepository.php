<?php

namespace App\Repositories\DeliveryMan;

use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use App\Traits\RepoResponseTrait;
use App\Traits\SmsSenderTrait;
use App\Models\PercelOtpLog;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class ReturnTaskRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait, SmsSenderTrait;

    protected $model;

    public function __construct(Parcel $model)
    {
        $this->model = $model;
    }

    public function complete($id)
    {
        $user = Sentinel::getUser();
        $deliveryMan = $user ? ($user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first()) : null;
        $deliveryManId = $deliveryMan ? $deliveryMan->id : 0;

        $parcel = $this->model->where('id', $id)
            ->where('return_delivery_man_id', $deliveryManId)
            ->first();

        if (!$parcel) {
            return [
                'success' => false,
                'message' => __('parcel_not_found'),
            ];
        }

        if ($parcel->status == 'returned-to-merchant') {
            return [
                'success' => false,
                'message' => __('this_parcel_has_already_been_returned'),
            ];
        }

        DB::beginTransaction();
        try {
            if ($parcel->status == 'partially-delivered' && $parcel->return_item_status == 'return_assigned_to_merchant') {
                $parcel->return_item_status = 'returned_to_merchant';
            } else {
                $parcel->status = 'returned-to-merchant';
            }
            $parcel->returned_date = Carbon::now();
            $parcel->otp = null;
            $parcel->otp_attempts = 0;
            $parcel->save();

            $event = new ParcelEvent();
            $event->parcel_id = $parcel->id;
            $event->title = 'parcel_return_to_merchant_event';
            $event->return_delivery_man_id = $deliveryManId;
            $event->cancel_note = null;
            $event->user_id = $user ? $user->id : null;
            $event->created_by = $user ? $user->id : null;
            $event->save();

            // Trigger Account/Financial Entries for Return (Merchant charge, Rider return fee, Company expense, VAT)
            try {
                $accountRepo = app(\App\Repositories\Interfaces\AccountInterface::class);
                $accountRepo->parcelStatusUpdate($parcel->id, 'returned-to-merchant');
            } catch (\Throwable $accEx) {
                // Keep event saved even if account rule fails
            }

            DB::commit();
            return [
                'success' => true,
                'message' => __('parcel_returned_to_merchant_successfully'),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => __('something_went_wrong_please_try_again'),
            ];
        }
    }

    public function resendOtp($id)
    {
        DB::beginTransaction();
        try {
            $user = Sentinel::getUser();
            $deliveryMan = $user ? ($user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first()) : null;
            $deliveryManId = $deliveryMan ? $deliveryMan->id : 0;

            $parcel = $this->model->where('id', $id)
                ->where('return_delivery_man_id', $deliveryManId)
                ->first();

            if (!$parcel) {
                return ['success' => false, 'message' => __('parcel_not_found')];
            }

            if ($parcel->status == 'returned-to-merchant') {
                return ['success' => false, 'message' => __('this_parcel_has_already_been_returned')];
            }

            $parcel->otp = rand(1000, 9999);
            $parcel->otp_expired_at = Carbon::now()->addMinutes(5);
            $parcel->otp_attempts = 0;
            $parcel->save();

            // Log OTP Resend
            try {
                PercelOtpLog::create([
                    'parcel_id'       => $parcel->id,
                    'delivery_man_id' => $deliveryManId,
                    'action'          => 'resend',
                    'otp_code'        => $parcel->otp,
                    'attempt_number'  => 0,
                    'source'          => 'rider_web',
                    'status_message'  => 'New Return OTP generated and sent to merchant',
                ]);
            } catch (\Exception $logEx) {}

            // Send SMS to Merchant
            try {
                $merchantPhone = $parcel->merchant->phone_number ?? ($parcel->merchant->user->phone_number ?? '');
                $sms_body = "Your return parcel verification code is {$parcel->otp}. Please share this code with the delivery man.";
                if ($merchantPhone) {
                    $this->send($sms_body, $merchantPhone);
                }
            } catch (\Exception $smsEx) {
            }

            DB::commit();
            return ['success' => true, 'message' => __('otp_sent_successfully')];
        } catch (\Throwable $th) {
            DB::rollback();
            return ['success' => false, 'message' => __('something_went_wrong_please_try_again')];
        }
    }

    public function verifyOtp($id, $otp)
    {
        $user = Sentinel::getUser();
        $deliveryMan = $user ? ($user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first()) : null;
        $deliveryManId = $deliveryMan ? $deliveryMan->id : 0;

        $parcel = $this->model->where('id', $id)
            ->where('return_delivery_man_id', $deliveryManId)
            ->first();

        if (!$parcel) {
            return ['success' => false, 'message' => __('parcel_not_found')];
        }

        if ($parcel->status == 'returned-to-merchant') {
            return ['success' => false, 'message' => __('this_parcel_has_already_been_returned')];
        }

        $maxAttempts = 3;

        // 1. Check max wrong attempts limit
        if ($parcel->otp_attempts >= $maxAttempts) {
            return [
                'success' => false,
                'message' => __('max_wrong_otp_attempts_reached_please_resend')
            ];
        }

        // 2. Check expiration time
        if (!empty($parcel->otp_expired_at) && Carbon::now()->gt(Carbon::parse($parcel->otp_expired_at))) {
            return ['success' => false, 'message' => __('otp_has_been_expired_please_resend')];
        }

        // 3. Check OTP match
        if ($parcel->otp == $otp) {
            return $this->complete($id);
        }

        // 4. Incorrect OTP: increment attempt count
        $parcel->otp_attempts = ($parcel->otp_attempts ?? 0) + 1;
        $parcel->save();

        $remaining = $maxAttempts - $parcel->otp_attempts;
        if ($remaining > 0) {
            $errorMsg = __('wrong_otp_code_remaining_attempts', ['remaining' => $remaining]);
        } else {
            $errorMsg = __('max_wrong_otp_attempts_reached_please_resend');
        }

        return ['success' => false, 'message' => $errorMsg];
    }
}
