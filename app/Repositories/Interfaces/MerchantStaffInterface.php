<?php

namespace App\Repositories\Interfaces;

interface MerchantStaffInterface{
    public function get($id);

    public function paginate($merchant);

    public function store($request);

    public function update($request);

    public function statusChange($request);

}
