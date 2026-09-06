<?php

namespace App\Observers;

use App\Models\Parcel;
use App\Services\WebhookService;

class ParcelObserver
{
    /**
     * Handle the Parcel "created" event.
     */
    public function created(Parcel $parcel): void
    {
        $merchant = $parcel->merchant;

        if ($merchant && !empty($merchant->webhook_url)) {
            $payload = [
                'event'             => 'order.created',
                'parcel_no'         => $parcel->parcel_no,
                'invoice_no'        => $parcel->customer_invoice_no,
                'status'            => $parcel->status,
                'customer_name'     => $parcel->customer_name,
                'customer_phone'    => $parcel->customer_phone_number,
                'collection_amount' => $parcel->price,
                'timestamp'         => now()->toDateTimeString(),
            ];

            WebhookService::send($merchant, $payload);
        }
    }

    /**
     * Handle the Parcel "updated" event.
     */
    public function updated(Parcel $parcel): void
    {
        if ($parcel->wasChanged('status') || $parcel->isDirty('status')) {
            $merchant = $parcel->merchant;

            if ($merchant && !empty($merchant->webhook_url)) {
                $statusEvent = 'parcel.' . strtolower(str_replace('-', '_', $parcel->status));

                $payload = [
                    'event'             => $statusEvent,
                    'parcel_no'         => $parcel->parcel_no,
                    'invoice_no'        => $parcel->customer_invoice_no,
                    'status'            => $parcel->status,
                    'customer_name'     => $parcel->customer_name,
                    'customer_phone'    => $parcel->customer_phone_number,
                    'collection_amount' => $parcel->price,
                    'timestamp'         => now()->toDateTimeString(),
                ];

                WebhookService::send($merchant, $payload);
            }
        }
    }
}
