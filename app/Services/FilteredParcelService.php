<?php

namespace App\Services;

use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilteredParcelService
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

    public function totalParcelStats($start_date, $end_date)
    {
        $now = now();
        $last12Months = [];
        $labels = [];
        // Prepare last 12 months labels and keys
        for ($i = 0; $i < 12; $i++) {
            $month = $now->copy()->subMonths(11 - $i);
            $last12Months[] = $month->format('Y-m'); // e.g., 2025-08
            $labels[] = $month->format('M');        // e.g., Aug
        }

        // Aggregate counts month-wise
        $queryResults = Parcel::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ])
            ->selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month_year,
            COUNT(*) as added_parcel_count,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as new_parcel_count,
            SUM(CASE WHEN status NOT IN ('pending','delivered','partially-delivered','returned-to-merchant','cancle','deleted') THEN 1 ELSE 0 END) as processing_count,
            SUM(CASE WHEN status IN ('delivered','partially-delivered') THEN 1 ELSE 0 END) as delivered_count
        ")
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get()
            ->keyBy('month_year'); // key by month for fast lookup
        // Aggregate total CODs and totals per status
        $totals = Parcel::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ])
            ->selectRaw("
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as total_new_parcel,
            SUM(CASE WHEN status = 'pending' THEN price ELSE 0 END) as total_new_parcel_cod,
            SUM(CASE WHEN status NOT IN ('pending','delivered','partially-delivered','returned-to-merchant','cancle','deleted') THEN 1 ELSE 0 END) as total_processing_parcel,
            SUM(CASE WHEN status NOT IN ('pending','delivered','partially-delivered','returned-to-merchant','cancle','deleted') THEN price ELSE 0 END) as total_processing_parcel_cod,
            SUM(CASE WHEN status IN ('delivered','partially-delivered') THEN 1 ELSE 0 END) as total_delivered_parcel,
            SUM(CASE WHEN status IN ('delivered','partially-delivered') THEN price ELSE 0 END) as total_delivered_parcel_cod
        ")
            ->first();

        // Prepare final arrays with month-wise data
        $data = [
            'labels' => $labels,
            'new_parcel' => [],
            'processing_parcel' => [],
            'delivered_parcel' => [],
            'total_new_parcel' => $totals->total_new_parcel ?? 0,
            'total_new_parcel_cod' => $totals->total_new_parcel_cod ?? 0,
            'total_processing_parcel' => $totals->total_processing_parcel ?? 0,
            'total_processing_parcel_cod' => $totals->total_processing_parcel_cod ?? 0,
            'total_delivered_parcel' => $totals->total_delivered_parcel ?? 0,
            'total_delivered_parcel_cod' => $totals->total_delivered_parcel_cod ?? 0,
        ];

        foreach ($last12Months as $monthKey) {
            $monthData = $queryResults->get($monthKey);
            $data['added_parcel'][] = (double) ($monthData->added_parcel_count ?? 0);
            $data['new_parcel'][] = (double) ($monthData->new_parcel_count ?? 0);
            $data['processing_parcel'][] = (double) ($monthData->processing_count ?? 0);
            $data['delivered_parcel'][] = (double) ($monthData->delivered_count ?? 0);
        }

        return $data;
    }

}
