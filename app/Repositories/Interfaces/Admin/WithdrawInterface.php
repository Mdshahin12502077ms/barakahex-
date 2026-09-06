<?php

namespace App\Repositories\Interfaces\Admin;

interface WithdrawInterface{

    public function all();

    public function chargeStatus($id, $status, $request);

    public function fileUpload($image);

    public function removeOldFile($image);

    public function updateBatch($id, $request);
}
