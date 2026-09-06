<?php

namespace App\Http\View\Composers;

use App\Models\Account\CompanyAccount;
use App\Traits\CommonHelperTrait;
use Illuminate\View\View;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class StaffBalanceComposer
{
    public function __construct()
    {

    }

    public function compose(View $view)
    {
        $total_income  = CompanyAccount::where('type','income')->where('user_id', Sentinel::getUser()->id)->sum('amount');
        $total_expense  = CompanyAccount::where('type','expense')->where('user_id', Sentinel::getUser()->id)->sum('amount');
        $remaining_balance = $total_income - $total_expense ;

        $view->with('balance', $remaining_balance);
    }
}
