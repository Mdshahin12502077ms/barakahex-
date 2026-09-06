<?php

namespace App\Repositories\Interfaces\Admin;

interface ExpenseInterface{

    public function all();

    public function store($data);

    public function get($id);

    public function update($data);

    public function delete($id);

    public function fileUpload($image);

    public function removeOldFile($image);

}
