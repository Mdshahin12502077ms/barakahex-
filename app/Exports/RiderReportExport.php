<?php

namespace App\Exports;

use App\Models\DeliveryMan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiderReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;
    protected $count = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $startDate = $this->request->get('from_date');
        $endDate   = $this->request->get('to_date');
        $branchId  = $this->request->get('branch');
        $riderId   = $this->request->get('rider_id');

        $query = DeliveryMan::with(['user.branch'])
            ->withCount([
                'parcels as assigned_count' => function ($q) use ($startDate, $endDate) {
                    if (!empty($startDate) && !empty($endDate)) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    }
                },
                'parcels as delivered_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered']);
                    if (!empty($startDate) && !empty($endDate)) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    }
                },
                'parcels as failed_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['cancelled', 'cancel', 'deleted']);
                    if (!empty($startDate) && !empty($endDate)) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    }
                },
                'parcels as return_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant']);
                    if (!empty($startDate) && !empty($endDate)) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    }
                },
            ])
            ->withSum([
                'parcels as total_cod' => function ($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered']);
                    if (!empty($startDate) && !empty($endDate)) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    }
                }
            ], 'price')
            ->withSum([
                'parcels as total_commission' => function ($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['delivered', 'delivered-and-verified', 'partially-delivered']);
                    if (!empty($startDate) && !empty($endDate)) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    }
                }
            ], 'delivery_fee');

        $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
        if (!hasPermission('read_all_delivery_man') && $user && !empty($user->branch_id)) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        } elseif (!empty($branchId) && $branchId != 'all') {
            $query->whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if (!empty($riderId)) {
            $query->where('id', $riderId);
        }

        return $query->latest('id')->get();
    }

    public function map($dm): array
    {
        $this->count++;
        $assigned = $dm->assigned_count ?? 0;
        $delivered = $dm->delivered_count ?? 0;
        $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 1) . '%' : '0%';

        $user = $dm->user;
        $name = $user ? $user->first_name . ' ' . $user->last_name : 'N/A';
        $phone = $dm->phone_number ?? ($user->phone_number ?? '');
        $branch = $user && $user->branch ? $user->branch->name : 'N/A';

        return [
            $this->count,
            $name,
            $phone,
            $branch,
            $assigned,
            $delivered,
            $dm->failed_count ?? 0,
            $dm->return_count ?? 0,
            $rate,
            number_format($dm->total_cod ?? 0, 2),
            number_format($dm->total_commission ?? 0, 2),
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Rider Name',
            'Phone',
            'Branch / Hub',
            'Assigned',
            'Delivered',
            'Failed',
            'Return',
            'Success Rate',
            'Total COD (BDT)',
            'Commission (BDT)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
