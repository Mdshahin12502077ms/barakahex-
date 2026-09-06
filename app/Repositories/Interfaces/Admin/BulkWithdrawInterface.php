<?php

namespace App\Repositories\Interfaces\Admin;

interface BulkWithdrawInterface{

    public function all();

    public function paginate();

    public function get($id);

    public function store($request);

    public function update($request);

    public function delete($id);

    public function changeStatus($id, $status, $account);

}
