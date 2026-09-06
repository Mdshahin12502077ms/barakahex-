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

class ChargesService
{
    /**
     * Monthly chart report (last 12 months VAS & Charge)
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

        // Aggregate month-wise vas & charge from delivered parcels
        $queryResults = Parcel::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ])
            ->where('status', 'delivered')
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_year,
                SUM(packaging_charge + fragile_charge + cod_charge) as total_vas,
                SUM(charge) as total_charge
            ")
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get()
            ->keyBy('month_year');

        // Final arrays
        $data = [
            'labels' => $labels,
            'vas' => [],
            'charge' => [],
        ];

        foreach ($last12Months as $monthKey) {
            $monthData = $queryResults->get($monthKey);
            $data['vas'][] = $monthData->total_vas ?? 0;
            $data['charge'][] = $monthData->total_charge ?? 0;
        }

        return $data;
    }

    /**
     * Totals for VAS & Charge in a given range
     */
    public function getTotals($start_date, $end_date)
    {
        $row = Parcel::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ])
            ->where('status', 'delivered')
            ->selectRaw("
                SUM(packaging_charge + fragile_charge + cod_charge) as total_vas,
                SUM(charge) as total_charge
            ")
            ->first();

        return [
            'total_vas' => $row->total_vas ?? 0,
            'total_charge' => $row->total_charge ?? 0,
        ];
    }

}
