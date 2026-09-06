<?php 

namespace App\Repositories\Interfaces;

interface DeliveryManInterface{
    public function all();
    
    public function activeAll();

    public function get($id);

    public function save($role, $data);

    public function store($data);

    public function update($data);

    public function delete($id);

    public function imageUpload($image, $type, $delivery_man_id);

    public function statusChange($data);

    public function filter($data);
}