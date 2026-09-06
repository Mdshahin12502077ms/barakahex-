<?php

namespace App\Services;

use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliverymanService
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

    public function totalDeliveryman()
    {
        $now = now();
        $start_date = $now->copy()->subMonths(11)->startOfMonth();
        $end_date = $now->copy()->endOfMonth();

        $query = DeliveryMan::where('status', 'active')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
                DB::raw('COUNT(*) as data')
            )
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->get();

        $map = $query->pluck('data', 'month_year')->toArray();

        $labels = [];
        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $now->copy()->subMonths(11 - $i);
            $monthKey = $month->format('Y-m');
            $labels[] = $month->format('M'); // e.g., Jan, Feb, Mar
            $data[] = (double) ($map[$monthKey] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
