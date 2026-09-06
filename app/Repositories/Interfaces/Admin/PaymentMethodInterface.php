<?php

namespace App\Repositories\Interfaces\Admin;

interface PaymentMethodInterface {
    public function get($id);

    public function paginate();

    public function store($request);

    public function update($request, $id);

    public function delete($id);

    public function statusChange($request);
}
