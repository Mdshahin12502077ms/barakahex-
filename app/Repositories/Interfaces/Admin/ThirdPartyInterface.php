<?php

namespace App\Repositories\Interfaces\Admin;

interface ThirdPartyInterface {
    public function get($id);

    public function paginate();

    public function store($request);

    public function update($request);

    public function delete($id);

    public function changeStatus($request);
}
