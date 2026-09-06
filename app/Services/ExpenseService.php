<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\MerchantWithdraw;
use App\Models\Account\DeliveryManAccount;
use App\Models\Account\Account;
use App\Models\Parcel;
use App\Models\Account\GovtVat;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    private $months = [
        'January'   => 'Jan',
        'February'  => 'Feb',
        'March'     => 'Mar',
        'April'     => 'Apr',
        'May'       => 'May',
        'June'      => 'Jun',
        'July'      => 'Jul',
        'August'    => 'Aug',
        'September' => 'Sep',
        'October'   => 'Oct',
        'November'  => 'Nov',
        'December'  => 'Dec',
    ];

    public function totalExpense($expense)
    {
        $data = [];

        $now = date('Y-m-d');


        $query = $expense->select(
                    DB::raw('MONTHNAME(created_at) as month_name'),
                    DB::raw('SUM(amount) as amount')
                )
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get();


        foreach ($this->months as $full_month => $sort_month) {
            $enrol = $query->firstWhere('month_name', $full_month);
            $data[] = $enrol ? $enrol->amount : 0;
        }

        return $data;
    }

}
