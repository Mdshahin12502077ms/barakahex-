<?php

namespace App\Repositories\Interfaces;

interface BulkInterface{

    public function get($id);

    public function bulkAssign($data);

    public function bulkTransferSave($data);

    public function bulkTransferReceive($data);

    public function bulkPickupAssign($data);

    public function parcelEvent($parcel_id, $title, $delivery_man, $pickup_man, $return_delivery_man);

}
