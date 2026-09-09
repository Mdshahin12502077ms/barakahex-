<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\hubDataTable;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Parcel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class HubReportController extends Controller
{
    public function index(hubDataTable $dataTable, Request $request)
    {
        $startDate = $request->get('from_date');
        $endDate   = $request->get('to_date');
        $branchId  = $request->get('branch_id');

        $user = Sentinel::getUser();
        $isRestricted = !hasPermission('read_all_parcel') && $user && !empty($user->branch_id);

        // Base Query for Summary Cards
        $parcelQuery = Parcel::query();

        if (!empty($startDate) && !empty($endDate)) {
            $parcelQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($isRestricted) {
            $parcelQuery->where('branch_id', $user->branch_id);
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            if (!empty($branchId) && $branchId !== 'all') {
                $parcelQuery->where('branch_id', $branchId);
            }
            $branches = Branch::all();
        }

        $summary = [
            'total_received'    => (clone $parcelQuery)->whereIn('status', ['received', 'transferred-received-by-branch'])->count(),
            'total_dispatched'  => (clone $parcelQuery)->whereIn('status', ['transferred-to-branch', 'delivery-assigned'])->count(),
            'in_branch_pending' => (clone $parcelQuery)->whereIn('status', ['pending', 'pickup-assigned', 're-schedule-pickup', 'received', 'transferred-received-by-branch', 're-schedule-delivery'])->count(),
            'total_delivered'   => (clone $parcelQuery)->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered'])->count(),
            'total_return'      => (clone $parcelQuery)->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant', 'cancel', 'cancelled'])->count(),
        ];

        return $dataTable->render('admin.reports.hub_report', compact('branches', 'summary'));
    }

    public function exportCsv(Request $request)
    {
        return (new hubDataTable())->csv();
    }
}
