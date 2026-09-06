<?php

namespace App\Repositories\Interfaces\Admin;

interface BankAccountInterface{
    
    public function all();

    public function accountsByUser($id);

    public function paginate();

    public function store($data);

    public function get($id);

    public function update($data);

    public function delete($id);

    public function bankRemainingBalance($table_name, $account_id, $row_id, $purpose);

}