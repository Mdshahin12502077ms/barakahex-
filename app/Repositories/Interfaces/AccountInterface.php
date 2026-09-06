<?php

namespace App\Repositories\Interfaces;

interface AccountInterface{

    public function all();

    public function get($id);

    public function paginate($limit);

    public function save($role, $data);

    public function store($data);

    public function update($data);

    public function delete($id);

    public function creditStore($data);

    public function creditUpdate($data);

    public function incomeExpenseManage($id, $status);

    public function incomeExpenseManageReverse($id, $status);

    public function incomeExpenseManageCancel($id, $status);

    public function withdrawReverseManage($id);
}
