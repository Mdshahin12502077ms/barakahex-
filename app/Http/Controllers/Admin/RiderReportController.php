<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\RiderReportDataTable;
use App\Models\Branch;
use App\Models\DeliveryMan;
use App\Models\Parcel;
use Illuminate\Http\Request;

class RiderReportController extends Controller
{
    public function index(RiderReportDataTable $dataTable, Request $request)
    {
        $startDate = $request->get('from_date');
        $endDate   = $request->get('to_date');
        $branchId  = $request->get('branch');
        $riderId   = $request->get('rider_id');

        $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
        $isRestricted = !hasPermission('read_all_delivery_man') && $user && !empty($user->branch_id);

        // Base Parcel Query for KPI Stats
        $parcelQuery = Parcel::query()->whereNotNull('delivery_man_id');

        if (!empty($startDate) && !empty($endDate)) {
            $parcelQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($isRestricted) {
            $parcelQuery->where('branch_id', $user->branch_id);
        } elseif (!empty($branchId) && $branchId != 'all') {
            $parcelQuery->where('branch_id', $branchId);
        }

        if (!empty($riderId)) {
            $parcelQuery->where('delivery_man_id', $riderId);
        }

        $totalAssigned = (clone $parcelQuery)->count();
        $totalDelivered = (clone $parcelQuery)->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered'])->count();
        $totalCod = (clone $parcelQuery)->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered'])->sum('price');
        $overallSuccessRate = $totalAssigned > 0 ? round(($totalDelivered / $totalAssigned) * 100, 1) : 0;

        $stats = [
            'total_assigned' => $totalAssigned,
            'total_delivered' => $totalDelivered,
            'total_cod' => $totalCod,
            'success_rate' => $overallSuccessRate,
        ];

        // Branches & Delivery Men for Filters
        if ($isRestricted) {
            $branches = Branch::where('id', $user->branch_id)->get();
            $delivery_men = DeliveryMan::whereHas('user', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->with('user')->get();
        } else {
            $branches = Branch::where('status', true)->get();
            $delivery_men = DeliveryMan::with('user')->get();
        }

        return $dataTable->render('admin.reports.rider_report', compact('stats', 'branches', 'delivery_men'));
    }

    public function exportCsv(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RiderReportExport($request),
            'Rider_Performance_Report_' . date('Ymd_His') . '.csv'
        );
    }
}
