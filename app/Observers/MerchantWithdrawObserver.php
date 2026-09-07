<?php

namespace App\Observers;

use App\Models\Account\MerchantWithdraw;
use App\Models\ParcelEvent;
use App\Services\WebhookService;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class MerchantWithdrawObserver
{
    /**
     * Handle the MerchantWithdraw "updated" event.
     */
    public function updated(MerchantWithdraw $withdraw): void
    {
        $user = Sentinel::getUser();
        $userId = $user ? $user->id : (auth()->check() ? auth()->id() : null);
        $ip = request()->ip() ?? '127.0.0.1';

        // 1. Audit Log: Settlement status change
        if ($withdraw->wasChanged('status')) {
            $oldStatus = $withdraw->getOriginal('status');
            $newStatus = $withdraw->status;
            $merchantName = $withdraw->merchant->company ?? ('Merchant #' . $withdraw->merchant_id);

            ParcelEvent::create([
                'parcel_id'       => null,
                'user_id'         => $userId,
                'branch_id'       => null,
                'title'           => 'settlement_' . strtolower(str_replace('-', '_', $newStatus)),
                'old_status'      => $oldStatus,
                'new_status'      => $newStatus,
                'ip_address'      => $ip,
                'action'          => 'settlement',
                'additional_info' => json_encode([
                    'type'            => 'settlement',
                    'withdraw_id'     => $withdraw->withdraw_id ?? $withdraw->id,
                    'merchant_id'     => $withdraw->merchant_id,
                    'merchant_name'   => $merchantName,
                    'amount'          => $withdraw->amount,
                    'old_status'      => $oldStatus,
                    'new_status'      => $newStatus,
                    'account_details' => $withdraw->account_details,
                    'description'     => "Settlement for {$merchantName} (Amount: {$withdraw->amount}) changed from {$oldStatus} to {$newStatus}",
                ]),
            ]);
        }

        // 2. Webhook Notification: Check if status changed to 'processed' (paid/settlement completed)
        if (($withdraw->wasChanged('status') || $withdraw->isDirty('status')) && $withdraw->status == 'processed') {
            $merchant = $withdraw->merchant;

            if ($merchant && !empty($merchant->webhook_url)) {
                $payload = [
                    'event'          => 'settlement.completed',
                    'withdraw_id'    => $withdraw->withdraw_id,
                    'amount'         => $withdraw->amount,
                    'status'         => 'paid',
                    'account_details'=> $withdraw->account_details,
                    'timestamp'      => now()->toDateTimeString(),
                ];

                WebhookService::send($merchant, $payload);
            }
        }
    }
}

