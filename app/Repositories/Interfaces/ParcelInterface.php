<?php

namespace App\Repositories\Interfaces;

interface ParcelInterface
{
    public function all();

    public function get($id);

    public function store($data);

    public function update($data);

    public function parcelDelete($request);

    public function getMerchants();

    public function getDeliveryMan();

    public function imageUpload($image, $type, $delivery_man_id);

    public function statusChange($data);

    public function assignPickupMan($data);

    public function assignDeliveryMan($data, $id, $type);

    public function reSchedulePickupMan($data);

    public function reScheduleDeliveryMan($data);

    public function parcelCancel($data);

    public function deliveryReverse($data);

    public function returnAssignToMerchant($data, $id);

    public function reSchedulePickup($data);

    public function reScheduleDelivery($data);

    public function parcelEvent($parcel_id, $title, $delivery_man = '', $pickup_man = '', $return_delivery_man = '', $cancel_note = '', $status = '', $branch = null, $transfer_delivery_man = null, $created_at = '');

    public function parcelStatusUpdate($id, $status, $note, $branch = null, $delivery_man = null);

    public function generate_random_string($length);

    public function reverseUpdate($id, $status, $note);

    //delivery reverse functions
    public function requestPending($id);

    public function requestPickupPending($request);

    public function requestPickupManReceivedPickupPending($request);

    public function uptoReceived($request);

    public function uptoDeliveryAssigned($request);
    //delivery reverse functions end

    //partial delivery
    public function partialDelivery($request);

    public function chargeDetails($request);
    //partial delviery ends

    public function customerDetails($request);

    public function trackParcel($id);
}
