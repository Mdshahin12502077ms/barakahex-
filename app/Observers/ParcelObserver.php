<?php

namespace App\Observers;

use App\Models\Parcel;
use App\Models\ParcelEvent;
use App\Services\WebhookService;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ParcelObserver
{
    /**
     * Build rich webhook payload for merchant
     */
    protected function buildPayload(Parcel $parcel, string $event): array
    {
        return [
            'event'             => $event,
            'consignment_id'    => $parcel->parcel_no,
            'parcel_no'         => $parcel->parcel_no,
            'invoice_no'        => $parcel->customer_invoice_no,
            'status'            => $parcel->status,
            'customer_name'     => $parcel->customer_name,
            'customer_phone'    => $parcel->customer_phone_number,
            'customer_address'  => $parcel->customer_address,
            'collection_amount' => (float) $parcel->price,
            'delivery_charge'   => (float) $parcel->total_delivery_charge,
            'payable_amount'    => (float) $parcel->payable,
            'district'          => $parcel->district->name ?? null,
            'thana'             => $parcel->thana->name ?? null,
            'note'              => $parcel->note,
            'tracking_url'      => $parcel->short_url ?? url('/tracking/' . $parcel->parcel_no),
            'timestamp'         => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle the Parcel "created" event.
     */
    public function created(Parcel $parcel): void
    {
        $merchant = $parcel->merchant;

        if ($merchant && !empty($merchant->webhook_url)) {
            $payload = $this->buildPayload($parcel, 'order.created');
            WebhookService::send($merchant, $payload);
        }
    }

    /**
     * Handle the Parcel "updated" event.
     */
    public function updated(Parcel $parcel): void
    {
        $user = Sentinel::getUser();
        $userId = $user ? $user->id : (auth()->check() ? auth()->id() : $parcel->user_id);
        $ip = request()->ip() ?? '127.0.0.1';

        // 1. Webhook Notification on Status Change
        if ($parcel->wasChanged('status') || $parcel->isDirty('status')) {
            $merchant = $parcel->merchant;

            if ($merchant && !empty($merchant->webhook_url)) {
                $statusEvent = 'parcel.' . strtolower(str_replace('-', '_', $parcel->status));
                $payload = $this->buildPayload($parcel, $statusEvent);
                WebhookService::send($merchant, $payload);
            }
        }

        // 2. Audit Log: COD (Price) Change
        if ($parcel->wasChanged('price')) {
            $oldCod = $parcel->getOriginal('price');
            $newCod = $parcel->price;
            ParcelEvent::create([
                'parcel_id'       => $parcel->id,
                'user_id'         => $userId,
                'branch_id'       => $parcel->branch_id,
                'title'           => 'parcel_cod_update_event',
                'old_status'      => $parcel->getOriginal('status') ?? $parcel->status,
                'new_status'      => $parcel->status,
                'ip_address'      => $ip,
                'action'          => 'cod_change',
                'additional_info' => json_encode([
                    'type'        => 'cod',
                    'old_value'   => $oldCod,
                    'new_value'   => $newCod,
                    'description' => "COD changed from {$oldCod} to {$newCod}",
                ]),
            ]);

            // Webhook notification for COD update
            $merchant = $parcel->merchant;
            if ($merchant && !empty($merchant->webhook_url)) {
                $payload = $this->buildPayload($parcel, 'parcel.cod_updated');
                $payload['old_cod'] = (float) $oldCod;
                $payload['new_cod'] = (float) $newCod;
                WebhookService::send($merchant, $payload);
            }
        }

        // 3. Audit Log: Delivery Charge Change
        if ($parcel->wasChanged('total_delivery_charge') || $parcel->wasChanged('charge') || $parcel->wasChanged('cod_charge')) {
            $oldCharge = $parcel->getOriginal('total_delivery_charge');
            $newCharge = $parcel->total_delivery_charge;
            ParcelEvent::create([
                'parcel_id'       => $parcel->id,
                'user_id'         => $userId,
                'branch_id'       => $parcel->branch_id,
                'title'           => 'parcel_charge_update_event',
                'old_status'      => $parcel->getOriginal('status') ?? $parcel->status,
                'new_status'      => $parcel->status,
                'ip_address'      => $ip,
                'action'          => 'charge_change',
                'additional_info' => json_encode([
                    'type'        => 'charge',
                    'old_charge'  => $oldCharge,
                    'new_charge'  => $newCharge,
                    'description' => "Delivery charge changed from {$oldCharge} to {$newCharge}",
                ]),
            ]);
        }

        // Record previous status so ParcelRepository::parcelEvent uses the true old_status
        if ($parcel->wasChanged('status')) {
            ParcelEvent::$lastOldStatus[$parcel->id] = $parcel->getOriginal('status');
        }
    }

    /**
     * Handle the Parcel "deleted" event.
     */
    public function deleted(Parcel $parcel): void
    {
        $merchant = $parcel->merchant;

        if ($merchant && !empty($merchant->webhook_url)) {
            $payload = $this->buildPayload($parcel, 'order.deleted');
            WebhookService::send($merchant, $payload);
        }
    }
}

