<?php

namespace App\Repositories\Interfaces\Role;

interface RoleInterface{
    public function all();

    public function paginate($limit);

    public function get($id);

    public function save($role, $data);

    public function store(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function statusChange($data);

}
