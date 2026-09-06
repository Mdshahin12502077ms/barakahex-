<?php

namespace App\Observers;

use App\Models\Account\MerchantWithdraw;
use App\Services\WebhookService;

class MerchantWithdrawObserver
{
    /**
     * Handle the MerchantWithdraw "updated" event.
     */
    public function updated(MerchantWithdraw $withdraw): void
    {
        // Check if status changed to 'processed' (which represents paid/settlement completed)
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
