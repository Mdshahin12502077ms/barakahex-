<?php

namespace App\Repositories\Interfaces;

interface MerchantWarehouseInterface{

    public function getMerchant($id);

    public function paginate($merchant);

    public function store($request);

    public function update($request);

    public function statusChange($request);
}
