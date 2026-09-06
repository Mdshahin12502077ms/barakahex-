<?php

namespace App\Repositories\Interfaces;

interface WithdrawInterface{

    public function all();

    public function get($id);

    public function paginate($limit);

    public function store($data);

    public function update($data);

    public function delete($id);

    public function statusChange($data);

    public function updatePaymentMethod($data);
    public function updateBankDetails($data);

    public function updateOthersAccount($data);

    public function chargeStatus($id, $status);

    public function fileUpload($image);

    public function removeOldFile($image);

    public function generate_random_string($length);

    public function paymentRequest($merchants);


}
