<?php

namespace App\Repositories\Interfaces;

interface MerchantProductInterface{

    public function getMerchant($id);

    public function paginate($merchant);

    public function store($request);

    public function update($request);

    public function statusChange($request);
}
