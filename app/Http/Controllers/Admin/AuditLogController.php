<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AuditLogDataTable;
use App\Http\Controllers\Controller;
use App\Models\ParcelEvent;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(AuditLogDataTable $dataTable)
    {
        $stats = [
            'total'          => ParcelEvent::count(),
            'status_change'  => ParcelEvent::where('action', 'status_change')->count(),
            'cod_change'     => ParcelEvent::where('action', 'cod_change')->count(),
            'settlement'     => ParcelEvent::where('action', 'settlement')->count(),
            'charge_change'  => ParcelEvent::where('action', 'charge_change')->count(),
            'rider_assign'   => ParcelEvent::where('action', 'rider_assign')->count(),
            'return'         => ParcelEvent::where('action', 'return')->count(),
        ];

        return $dataTable->render('admin.audit-logs.index', compact('stats'));
    }
}
