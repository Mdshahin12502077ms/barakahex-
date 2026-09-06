<?php

namespace App\Services;

use App\Models\Account\CompanyAccount;
use App\Models\Parcel;

class FinanceReportService
{
    private $months = [
        'January' => 'Jan',
        'February' => 'Feb',
        'March' => 'Mar',
        'April' => 'Apr',
        'May' => 'May',
        'June' => 'Jun',
        'July' => 'Jul',
        'August' => 'Aug',
        'September' => 'Sep',
        'October' => 'Oct',
        'November' => 'Nov',
        'December' => 'Dec',
    ];

    /**
     * Monthly chart report (last 12 months income, expense, profit)
     */
    public function getMonthlyReport($start_date, $end_date)
    {
        $now = now();
        $last12Months = [];
        $labels = [];

        // Prepare last 12 months labels and keys
        for ($i = 0; $i < 12; $i++) {
            $month = $now->copy()->subMonths(11 - $i);
            $last12Months[] = $month->format('Y-m'); // e.g., 2025-08
            $labels[] = $month->format('M');         // e.g., Aug
        }

        // Aggregate month-wise income & expense
        $queryResults = CompanyAccount::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ])
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_year,
                SUM(CASE WHEN type = 'income' 
                    AND create_type = 'user_defined'
                    AND source IN ('delivery_charge_receive_from_merchant','cash_receive_from_delivery_man')
                    THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' 
                    AND create_type = 'user_defined'
                    THEN amount ELSE 0 END) as expense
            ")
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get()
            ->keyBy('month_year');

        // Prepare final arrays with month-wise data
        $data = [
            'labels' => $labels,
            'income' => [],
            'expense' => [],
            'profit' => [],
        ];

        foreach ($last12Months as $monthKey) {
            $monthData = $queryResults->get($monthKey);
            $income = $monthData->income ?? 0;
            $expense = $monthData->expense ?? 0;
            $profit = $income - $expense;

            $data['income'][] = (double) $income;
            $data['expense'][] = (double) $expense;
            $data['profit'][] = (double) $profit;
        }

        return $data;
    }

    /**
     * Totals for a given date range
     */
    public function getTotals($start_date, $end_date)
    {

        $row = CompanyAccount::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ])
            ->selectRaw("
                SUM(CASE WHEN type = 'income' 
                    AND create_type = 'user_defined'
                    AND source IN ('delivery_charge_receive_from_merchant','cash_receive_from_delivery_man')
                    THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' 
                    AND create_type = 'user_defined'
                    THEN amount ELSE 0 END) as total_expense
            ")
            ->first();

        $income = $row->total_income ?? 0;
        $expense = $row->total_expense ?? 0;
        $profit = $income - $expense;

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'total_profit' => $profit,
        ];
    }

}
