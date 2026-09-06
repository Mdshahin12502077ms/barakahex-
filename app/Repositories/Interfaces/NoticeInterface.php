<?php

namespace App\Repositories\Interfaces;

interface NoticeInterface{

    public function get($id);

    public function paginate($limit);

    public function store($request);

    public function update($request);

    public function statusChange($request);

    public function delete($id);

}
