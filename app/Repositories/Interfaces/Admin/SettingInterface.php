<?php

namespace App\Repositories\Interfaces\Admin;

interface SettingInterface{

    public function store($data);

    public function arrayStore($data);

    public function packingCharge();

    public function packingChargeAdd();

    public function deletePackagingCharge($id);

    public function packagingChargeUpdate($request);

    public function chargeUpdate($request);


}
