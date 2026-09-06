<?php

namespace App\Traits;
use App\Models\Account\CompanyAccount;
use App\Models\Account\Account;
use App\Models\StaffAccount;

trait CommonHelperTrait {

    public function IncomeExpenseBalance() {
        $account = CompanyAccount::orderByDesc('id')->first();
        if(!blank($account)){
            return $account->balance;
        }
        return 0;
    }

    public function StaffLatestBalance($account_id) {
        $user_id = Account::find($account_id)->user_id;

        return StaffAccount::where('user_id', $user_id)->orderByDesc('id')->first()->balance;
    }

    public function checkRoutingNo($routing_no, $payment_to){
        if (strtolower($payment_to) == 'bank'):
            if ($routing_no == ''):
                return true;
            endif;
        endif;
        return false;
    }
}
