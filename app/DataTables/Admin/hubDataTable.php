<?php

namespace App\DataTables\Admin;

use App\Models\Branch;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class hubDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name', function ($branch) {
                return '<span class="fw-semibold text-dark font-14">' . e($branch->name) . '</span>';
            })
            ->addColumn('type', function ($branch) {
                $type = strtolower($branch->type ?? 'branch');
                $label = match($type) {
                    'head_office' => __('head_office'),
                    'hub'         => __('hub'),
                    'sort_center' => __('sort_center'),
                    default       => __('branch'),
                };
                $bg = match($type) {
                    'head_office' => '#0f766e',
                    'hub'         => '#0284c7',
                    'sort_center' => '#0891b2',
                    default       => '#6366f1',
                };
                return '<span class="badge rounded-pill px-3 py-1 fw-semibold font-12" style="background: ' . $bg . '; color: #ffffff;">' . e($label) . '</span>';
            })
            ->addColumn('received', function ($branch) {
                return '<span class="fw-medium font-14">' . number_format($branch->received_count ?? 0) . '</span>';
            })
            ->addColumn('dispatched', function ($branch) {
                return '<span class="fw-medium font-14">' . number_format($branch->dispatched_count ?? 0) . '</span>';
            })
            ->addColumn('pending', function ($branch) {
                return '<span class="fw-medium font-14">' . number_format($branch->pending_count ?? 0) . '</span>';
            })
            ->addColumn('delivered', function ($branch) {
                return '<span class="fw-medium font-14">' . number_format($branch->delivered_count ?? 0) . '</span>';
            })
            ->addColumn('return', function ($branch) {
                $return = $branch->return_count ?? 0;
                $color = $return > 0 ? 'text-danger' : 'text-dark';
                return '<span class="fw-medium font-14 ' . $color . '">' . number_format($return) . '</span>';
            })
            ->addColumn('delivery_rate', function ($branch) {
                $delivered = $branch->delivered_count ?? 0;
                $total = $delivered + ($branch->return_count ?? 0) + ($branch->pending_count ?? 0);
                if ($total == 0) {
                    $total = $branch->dispatched_count ?? 0;
                }
                $rate = $total > 0 ? min(100, round(($delivered / $total) * 100)) : 0;

                return '
                    <div class="d-flex align-items-center gap-2" style="min-width: 140px;">
                        <div class="progress flex-grow-1" style="height: 6px; background-color: #e2e8f0; border-radius: 999px;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: ' . $rate . '%; background-color: #10b981;" 
                                 aria-valuenow="' . $rate . '" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <span class="fw-bold font-13" style="color: #334155; min-width: 38px;">' . $rate . '%</span>
                    </div>';
            })
            ->rawColumns(['name', 'type', 'received', 'dispatched', 'pending', 'delivered', 'return', 'delivery_rate'])
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        $startDate = $this->request->get('from_date');
        $endDate   = $this->request->get('to_date');
        $branchId  = $this->request->get('branch_id');

        $query = Branch::query();

        // Permission Scoping: Restrict branch managers to own branch
        $user = Sentinel::getUser();
        if (!hasPermission('read_all_parcel') && $user && !empty($user->branch_id)) {
            $query->where('id', $user->branch_id);
        } elseif (!empty($branchId) && $branchId !== 'all') {
            $query->where('id', $branchId);
        }

        // Subquery counts matching status definitions
        $query->withCount([
            'parcels as received_count' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['received', 'transferred-received-by-branch']);
                if (!empty($startDate) && !empty($endDate)) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
            },
            'parcels as dispatched_count' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['transferred-to-branch', 'delivery-assigned']);
                if (!empty($startDate) && !empty($endDate)) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
            },
            'parcels as pending_count' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['pending', 'pickup-assigned', 're-schedule-pickup', 'received', 'transferred-received-by-branch', 're-schedule-delivery']);
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
            'parcels as return_count' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant', 'cancel', 'cancelled']);
                if (!empty($startDate) && !empty($endDate)) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
            },
        ]);

        return $query->latest('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:100%')
            ->parameters([
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [
                        'extend' => 'csv',
                        'text' => '<i class="las la-file-csv"></i> ' . __('export_to_csv'),
                        'className' => 'btn btn-sm btn-success',
                    ],
                    [
                        'extend' => 'print',
                        'text' => '<i class="las la-print"></i> ' . __('print'),
                        'className' => 'btn btn-sm btn-info text-white',
                    ],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ ' . __('per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title(__('Branch / Hub Name'))->addClass('ps-3'),
            Column::computed('type')->title(__('Type')),
            Column::computed('received')->title(__('Received'))->addClass('text-center'),
            Column::computed('dispatched')->title(__('Dispatched'))->addClass('text-center'),
            Column::computed('pending')->title(__('Pending'))->addClass('text-center'),
            Column::computed('delivered')->title(__('Delivered'))->addClass('text-center'),
            Column::computed('return')->title(__('Return'))->addClass('text-center'),
            Column::computed('delivery_rate')->title(__('Delivery Rate'))->addClass('pe-3'),
        ];
    }

    protected function filename(): string
    {
        return 'HubReport_' . date('YmdHis');
    }
}
