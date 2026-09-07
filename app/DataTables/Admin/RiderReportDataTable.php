<?php

namespace App\DataTables\Admin;

use App\Models\DeliveryMan;
use App\Models\Parcel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RiderReportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('rider_info', function ($delivery_man) {
                $user = $delivery_man->user;
                $name = $user ? $user->first_name . ' ' . $user->last_name : 'N/A';
                $phone = $delivery_man->phone_number ?? ($user->phone_number ?? '');
                $branchName = $user && $user->branch ? $user->branch->name : 'N/A';
                $image = $user ? getFileLink('80X80', $user->image_id) : static_asset('admin/images/default/user40x40.jpg');

                return '
                    <div class="user-info-panel d-flex gap-12 align-items-center">
                        <div class="user-img" style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden;">
                            <img src="' . $image . '" alt="' . e($name) . '" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div class="user-info">
                            <h6 class="mb-0 fw-bold">' . e($name) . '</h6>
                            <small class="text-muted">' . e($phone) . ($branchName != 'N/A' ? ' &bull; ' . e($branchName) : '') . '</small>
                        </div>
                    </div>';
            })
            ->addColumn('assigned', function ($delivery_man) {
                return '<span class="badge bg-primary text-white font-13 px-3 py-2" style="background-color: #3b82f6 !important;">' . number_format($delivery_man->assigned_count) . '</span>';
            })
            ->addColumn('delivered', function ($delivery_man) {
                return '<span class="badge bg-success text-white font-13 px-3 py-2" style="background-color: #10b981 !important;">' . number_format($delivery_man->delivered_count) . '</span>';
            })
            ->addColumn('failed', function ($delivery_man) {
                return '<span class="badge bg-danger text-white font-13 px-3 py-2" style="background-color: #ef4444 !important;">' . number_format($delivery_man->failed_count) . '</span>';
            })
            ->addColumn('return', function ($delivery_man) {
                return '<span class="badge bg-dark text-white font-13 px-3 py-2" style="background-color: #334155 !important;">' . number_format($delivery_man->return_count) . '</span>';
            })
            ->addColumn('success_rate', function ($delivery_man) {
                $assigned = $delivery_man->assigned_count;
                $delivered = $delivery_man->delivered_count;
                $rate = $assigned > 0 ? round(($delivered / $assigned) * 100, 1) : 0;
                $barColor = $rate >= 80 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');

                return '
                    <div style="min-width: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-12 text-muted fw-bold">Success Rate</span>
                            <span class="font-12 fw-bold">' . $rate . '%</span>
                        </div>
                        <div class="progress" style="height: 6px; background-color: #e2e8f0; border-radius: 4px;">
                            <div class="progress-bar" role="progressbar" style="width: ' . $rate . '%; background-color: ' . $barColor . ';" aria-valuenow="' . $rate . '" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>';
            })
            ->addColumn('cod_amount', function ($delivery_man) {
                return '<span class="fw-bold text-dark font-14">&#2547; ' . number_format($delivery_man->total_cod ?? 0, 2) . '</span>';
            })
            ->addColumn('commission', function ($delivery_man) {
                return '<span class="fw-bold text-dark font-14">&#2547; ' . number_format($delivery_man->total_commission ?? 0, 2) . '</span>';
            })
            ->rawColumns(['rider_info', 'assigned', 'delivered', 'failed', 'return', 'success_rate', 'cod_amount', 'commission'])
            ->setRowId('id');
    }

    public function query(): QueryBuilder
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

        // Branch Scoping: If user cannot read all delivery man (e.g. Branch Manager), restrict to own branch
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

        // Specific Rider filter
        if (!empty($riderId)) {
            $query->where('id', $riderId);
        }

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
                        'extend' => 'pdf',
                        'text' => '<i class="las la-file-pdf"></i> ' . __('export_to_pdf'),
                        'className' => 'btn btn-sm btn-danger',
                    ],
                    [
                        'extend' => 'print',
                        'text' => '<i class="las la-print"></i> ' . __('print'),
                        'className' => 'btn btn-sm btn-info',
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
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('rider_info')->title(__('rider_info')),
            Column::computed('assigned')->title(__('assigned'))->addClass('text-center'),
            Column::computed('delivered')->title(__('delivered'))->addClass('text-center'),
            Column::computed('failed')->title(__('failed'))->addClass('text-center'),
            Column::computed('return')->title(__('return'))->addClass('text-center'),
            Column::computed('success_rate')->title(__('success_rate'))->addClass('text-center'),
            Column::computed('cod_amount')->title(__('cod_amount') . ' (&#2547;)')->addClass('text-end'),
            Column::computed('commission')->title(__('commission') . ' (&#2547;)')->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'RiderReport_' . date('YmdHis');
    }
}
