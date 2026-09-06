<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\Account\CompanyAccount;
use Illuminate\Support\Facades\DB;

class ProfitService
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

    public function totalProfit($start_date, $end_date)
    {
        $data = [];

        $income = CompanyAccount::where('type', 'income')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('create_type', 'user_defined')
            ->whereIn('source', ['delivery_charge_receive_from_merchant', 'cash_receive_from_delivery_man'])
            ->select(
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $expense = CompanyAccount::where(function ($query) use ($start_date, $end_date) {
                $query->whereDate('created_at', '>=', $start_date)
                      ->whereDate('created_at', '<=', $end_date);
            })
            ->where('type', 'expense')
            ->where('create_type', 'user_defined')
            ->select(
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy(DB::raw('MONTH(created_at)'), 'month_name')
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();


        foreach ($this->months as $full_month => $sort_month) {
            $income_amount  = $income->where('month_name', $full_month)->first();
            $expense_amount = $expense->where('month_name', $full_month)->first();
            $income_value   = $income_amount ? $income_amount->amount : 0;
            $expense_value  = $expense_amount ? $expense_amount->amount : 0;
            $profit         = $income_value - $expense_value;
            $data[]         = $profit ? $profit : 0;

        }

        return $data;
    }
}

