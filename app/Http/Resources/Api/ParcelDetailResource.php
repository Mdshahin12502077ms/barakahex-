<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ParcelDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $merchant_name = '';
        $email = '';
        $pickup_date = $this->pickup_date ? date('M d, Y', strtotime($this->pickup_date)) : '';
        $delivery_date = $this->delivery_date ? date('M d, Y', strtotime($this->delivery_date)) : '';

        if ($this->user->user_type == 'merchant') {
            $merchant_name = $this->user->first_name . ' ' . $this->user->last_name;
            $email = $this->user->email;
        } elseif ($this->user->user_type == 'merchant_staff') {
            $merchant_name = $this->merchant->user->first_name . ' ' . $this->merchant->user->last_name;
            $email = $this->merchant->user->email;
        }

        $status = $this->status;
        $total_delivery_charge = $this->total_delivery_charge;

        $events = [];
        foreach ($this->events as $event) {
            $eventData = [
                'created_at' => date('M d, Y H:i:s', strtotime($event->created_at)),
                'title' => __($event->title),
                'cancel_note' => $event->cancel_note ?: null,
            ];

            if ($event->title == 'assign_pickup_man_event' || $event->title == 'parcel_re_schedule_pickup_event') {
                $eventData['pickup_man'] = @$event->pickupPerson ? $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name : null;
                $eventData['pickup_phone_number'] = @$event->pickupPerson ? $event->pickupPerson->phone_number : null;
            }

            if ($event->title == 'assign_delivery_man_event' || $event->title == 'parcel_re_schedule_delivery_event') {
                if ($this->location == 'dhaka') {
                    $eventData['delivery_man'] = $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name;
                    $eventData['delivery_phone_number'] = $event->deliveryPerson->phone_number;
                } else {
                    $eventData['delivery_man'] = null;
                    $eventData['delivery_phone_number'] = null;
                }
            }

            $events[] = $eventData;
        }

        $data = (object)[
            'id' => (int)$this->id,
            'company_name' => $this->merchant->company,
            'merchant_name' => $merchant_name,
            'pickup_number' => $this->pickup_shop_phone_number,
            'pickup_address' => $this->pickup_address,
            'email' => $email,
            'pickup_date' => $pickup_date,
            'delivery_date' => $delivery_date,
            'parcel_type' => $this->parcel_type,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
            'total_charge' => $total_delivery_charge,
            'customer_invoice_no' => $this->customer_invoice_no,
            'customer_name' => $this->customer_name,
            'customer_phone_number' => $this->customer_phone_number,
            'customer_address' => $this->customer_address,
            'location' => $this->location,
            'weight' => $this->weight,
            'price' => $this->price,
            'events' => $events,
        ];

        return $data;
    }
}
