<?php

namespace App\Repositories\DeliveryMan;

use App\Models\CustomerParcelSmsTemplates;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Models\PercelOtpLog;
use App\Repositories\Interfaces\AccountInterface;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\RepoResponseTrait;
use App\Traits\SmsSenderTrait;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class MyDeliveryRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait, SmsSenderTrait;

    protected $model;
    protected $accounts;

    public function __construct(Parcel $model, AccountInterface $accounts)
    {
        $this->model = $model;
        $this->accounts = $accounts;
    }

    private function getDeliveryMan()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return null;
        }
        return $user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first();
    }

    public function all($request = null, $paginate = 15)
    {
        $deliveryMan = $this->getDeliveryMan();
        if (!$deliveryMan) {
            return collect();
        }

        $query = $this->model->with(['merchant.user', 'shop', 'events'])
            ->where('delivery_man_id', $deliveryMan->id);

        if ($request && $request->has('status') && !empty($request->status) && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request && $request->has('date') && !empty($request->date)) {
            $query->whereDate('date', $request->date);
        }

        if ($request && $request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('parcel_no', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_invoice_no', 'LIKE', "%{$search}%");
            });
        }

        return $query->latest('id')->paginate($paginate);
    }

    public function find($id)
    {
        $deliveryMan = $this->getDeliveryMan();
        $deliveryManId = $deliveryMan->id ?? 0;
        return $this->model->with(['merchant.user', 'shop', 'events.user', 'events.pickupPerson.user', 'events.deliveryPerson.user', 'otpLogs'])
            ->where(function ($q) use ($deliveryManId) {
                $q->where('delivery_man_id', $deliveryManId)
                  ->orWhere('pickup_man_id', $deliveryManId);
            })
            ->findOrFail($id);
    }

    public function delivered($id, $request = null)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('delivery_man_id', $deliveryManId)
                      ->orWhere('pickup_man_id', $deliveryManId);
                })
                ->firstOrFail();

            if (in_array($parcel->status, ['delivered', 'delivered-and-verified'])) {
                return false;
            }

            // Assign delivery_man_id to current rider if not assigned yet
            if (empty($parcel->delivery_man_id)) {
                $parcel->delivery_man_id = $deliveryManId;
            }

            $parcel->date = date('Y-m-d');
            $parcel->status = 'delivered';
            $parcel->otp = rand(1000, 9999);
            $parcel->otp_expired_at = Carbon::now()->addMinutes(5);
            $parcel->otp_attempts = 0;
            $parcel->save();

            // Manage Income / Expense / COD Accounting
            $this->accounts->incomeExpenseManage($parcel->id, 'delivered');

            // Log event
            $this->parcelEvent($parcel->id, 'parcel_delivered_event', $deliveryManId, '', '', $request->note ?? 'Delivered by delivery man');

            // Log OTP Generation
            try {
                PercelOtpLog::create([
                    'parcel_id'       => $parcel->id,
                    'delivery_man_id' => $deliveryManId,
                    'action'          => 'generated',
                    'otp_code'        => $parcel->otp,
                    'attempt_number'  => 0,
                    'source'          => 'rider_web',
                    'status_message'  => 'Delivery OTP generated and sent to customer',
                ]);
            } catch (\Exception $logEx) {
            }

            // Send SMS with OTP to Customer if template is active
            try {
                $sms_template = CustomerParcelSmsTemplates::where('subject', 'delivery_confirm_otp')->first();
                if ($sms_template && $sms_template->sms_to_customer) {
                    $sms_body = str_replace('{merchant_name}', @$parcel->merchant->company, $sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{otp}', $parcel->otp, $sms_body);
                    $sms_body = str_replace('{our_company_name}', setting('company_name') ?: __('app_name'), $sms_body);
                    $this->send($sms_body, $parcel->customer_phone_number, '', '', $sms_template->masking);
                }
            } catch (\Exception $smsEx) {
                // Keep delivery successful even if SMS gateway has network timeout
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function verifyOtp($id, $otp)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('delivery_man_id', $deliveryManId)
                      ->orWhere('pickup_man_id', $deliveryManId);
                })
                ->firstOrFail();

            $maxAttempts = 3;

            // 1. Check max wrong attempts limit
            if ($parcel->otp_attempts >= $maxAttempts) {
                try {
                    PercelOtpLog::create([
                        'parcel_id'       => $parcel->id,
                        'delivery_man_id' => $deliveryManId,
                        'action'          => 'locked',
                        'otp_code'        => $parcel->otp,
                        'submitted_otp'   => $otp,
                        'attempt_number'  => $parcel->otp_attempts,
                        'source'          => 'rider_web',
                        'status_message'  => 'Maximum wrong attempts reached. Verification blocked.',
                    ]);
                } catch (\Exception $logEx) {}

                return [
                    'status' => false,
                    'message' => __('max_wrong_otp_attempts_reached_please_resend')
                ];
            }

            // 2. Check expiration time
            if (!empty($parcel->otp_expired_at) && Carbon::now()->gt(Carbon::parse($parcel->otp_expired_at))) {
                try {
                    PercelOtpLog::create([
                        'parcel_id'       => $parcel->id,
                        'delivery_man_id' => $deliveryManId,
                        'action'          => 'expired',
                        'otp_code'        => $parcel->otp,
                        'submitted_otp'   => $otp,
                        'attempt_number'  => $parcel->otp_attempts,
                        'source'          => 'rider_web',
                        'status_message'  => 'Submitted OTP has expired',
                    ]);
                } catch (\Exception $logEx) {}

                return ['status' => false, 'message' => __('otp_has_been_expired_please_resend')];
            }

            // 3. Check OTP match
            if ($parcel->otp == $otp) {
                $parcel->status = 'delivered-and-verified';
                $parcel->date = date('Y-m-d');
                $parcel->otp_attempts = 0;
                $parcel->save();

                $this->parcelEvent($parcel->id, 'parcel_delivered_and_verified_event', $deliveryManId, '', '', 'Delivery successfully verified via OTP');

                try {
                    PercelOtpLog::create([
                        'parcel_id'       => $parcel->id,
                        'delivery_man_id' => $deliveryManId,
                        'action'          => 'success',
                        'otp_code'        => $parcel->otp,
                        'submitted_otp'   => $otp,
                        'attempt_number'  => 1,
                        'source'          => 'rider_web',
                        'status_message'  => 'Delivery OTP verified successfully',
                    ]);
                } catch (\Exception $logEx) {}

                DB::commit();
                return ['status' => true, 'message' => __('delivery_successfully_verified')];
            }

            // 4. Incorrect OTP: increment attempt count
            $parcel->otp_attempts = ($parcel->otp_attempts ?? 0) + 1;
            $parcel->save();

            $isLocked = ($parcel->otp_attempts >= $maxAttempts);
            try {
                PercelOtpLog::create([
                    'parcel_id'       => $parcel->id,
                    'delivery_man_id' => $deliveryManId,
                    'action'          => $isLocked ? 'locked' : 'wrong_attempt',
                    'otp_code'        => $parcel->otp,
                    'submitted_otp'   => $otp,
                    'attempt_number'  => $parcel->otp_attempts,
                    'source'          => 'rider_web',
                    'status_message'  => $isLocked ? 'Maximum 3 wrong attempts reached. OTP locked.' : 'Incorrect OTP code entered',
                ]);
            } catch (\Exception $logEx) {}

            DB::commit();

            $remaining = $maxAttempts - $parcel->otp_attempts;
            if ($remaining > 0) {
                $errorMsg = __('wrong_otp_code_remaining_attempts', ['remaining' => $remaining]);
            } else {
                $errorMsg = __('max_wrong_otp_attempts_reached_please_resend');
            }

            return ['status' => false, 'message' => $errorMsg];
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function resendOtp($id)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('delivery_man_id', $deliveryManId)
                      ->orWhere('pickup_man_id', $deliveryManId);
                })
                ->firstOrFail();

            if ($parcel->status != 'delivered') {
                return false;
            }

            $parcel->otp = rand(1000, 9999);
            $parcel->otp_expired_at = Carbon::now()->addMinutes(5);
            $parcel->otp_attempts = 0; // Reset wrong attempts counter on resend
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
                    'status_message'  => 'New OTP generated and resent to customer',
                ]);
            } catch (\Exception $logEx) {}

            // Send SMS with OTP to Customer if template is active
            try {
                $sms_template = CustomerParcelSmsTemplates::where('subject', 'delivery_confirm_otp')->first();
                if ($sms_template && $sms_template->sms_to_customer) {
                    $sms_body = str_replace('{merchant_name}', @$parcel->merchant->company, $sms_template->content);
                    $sms_body = str_replace('{parcel_id}', $parcel->parcel_no, $sms_body);
                    $sms_body = str_replace('{otp}', $parcel->otp, $sms_body);
                    $sms_body = str_replace('{our_company_name}', setting('company_name') ?: __('app_name'), $sms_body);
                    $this->send($sms_body, $parcel->customer_phone_number, '', '', $sms_template->masking);
                }
            } catch (\Exception $smsEx) {
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function rescheduleDelivery($id, $request)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('delivery_man_id', $deliveryManId)
                      ->orWhere('pickup_man_id', $deliveryManId);
                })
                ->firstOrFail();

            $parcel->status = 're-schedule-delivery';
            if ($request && $request->delivery_date) {
                $parcel->delivery_date = $request->delivery_date;
            }
            $parcel->save();

            $reason = $request->note ?: ($request->reason_type ?: ($request->reason ?: null));
            if ($reason === 'Other' && !empty($request->custom_note)) {
                $reason = 'Other: ' . $request->custom_note;
            } elseif (!empty($request->custom_note) && $reason && $reason !== $request->custom_note) {
                $reason = $reason . ' - ' . $request->custom_note;
            }
            $reason = $reason ?: 'Delivery rescheduled by delivery man';

            $this->parcelEvent($parcel->id, 'parcel_re_schedule_delivery_event', $deliveryManId, '', '', $reason);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function cancelDelivery($id, $request)
    {
        DB::beginTransaction();
        try {
            $deliveryMan = $this->getDeliveryMan();
            $deliveryManId = $deliveryMan->id ?? 0;

            $parcel = $this->model->where('id', $id)
                ->where(function ($q) use ($deliveryManId) {
                    $q->where('delivery_man_id', $deliveryManId)
                      ->orWhere('pickup_man_id', $deliveryManId);
                })
                ->firstOrFail();

            $parcel->status = 'cancel';
            $parcel->save();

            $reason = $request->note ?: ($request->reason_type ?: ($request->reason ?: null));
            if ($reason === 'Other' && !empty($request->custom_note)) {
                $reason = 'Other: ' . $request->custom_note;
            } elseif (!empty($request->custom_note) && $reason && $reason !== $request->custom_note) {
                $reason = $reason . ' - ' . $request->custom_note;
            }
            $reason = $reason ?: 'Delivery cancelled by customer/delivery man';

            $this->parcelEvent($parcel->id, 'parcel_cancel_event', $deliveryManId, '', '', $reason);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function statistics()
    {
        $deliveryMan = $this->getDeliveryMan();
        if (!$deliveryMan) {
            return [
                'total'      => 0,
                'processing' => 0,
                'completed'  => 0,
                'rescheduled'=> 0,
                'cancelled'  => 0,
            ];
        }

        $base = $this->model->where('delivery_man_id', $deliveryMan->id);

        return [
            'total'       => (clone $base)->count(),
            'processing'  => (clone $base)->whereIn('status', ['delivery_assigned', 'delivery-assigned', 'processing', 'out-for-delivery'])->count(),
            'completed'   => (clone $base)->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered'])->count(),
            'rescheduled' => (clone $base)->whereIn('status', ['re-schedule-delivery', 'delivery_re_schedule'])->count(),
            'cancelled'   => (clone $base)->whereIn('status', ['cancel', 'cancelled'])->count(),
        ];
    }

    private function parcelEvent($parcel_id, $title, $delivery_man = '', $pickup_man = '', $return_delivery_man = '', $note = '')
    {
        $event = new ParcelEvent();
        $event->parcel_id = $parcel_id;
        $event->title = $title;
        $event->delivery_man_id = $delivery_man ?: null;
        $event->pickup_man_id = $pickup_man ?: null;
        $event->return_delivery_man_id = $return_delivery_man ?: null;
        $event->cancel_note = $note;
        $event->user_id = Sentinel::check() ? Sentinel::getUser()->id : null;
        $event->created_by = Sentinel::check() ? Sentinel::getUser()->id : null;
        $event->save();
        return $event;
    }
}
