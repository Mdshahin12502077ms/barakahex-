<?php

namespace App\Repositories\Interfaces;

interface UserInterface{
    public function all();

    public function paginate($limit);

    public function get($id);

    public function store($data);

    public function update($data);

    public function delete($id);

    public function statusChange($data);

    public function updateProfile($request);
}
