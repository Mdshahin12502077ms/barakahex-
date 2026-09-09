<?php

namespace App\DataTables\Admin;

use App\Models\Parcel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FinancialDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                $dateFormatted = !empty($row->date) ? date('d M, Y', strtotime($row->date)) : 'N/A';
                $dayOfWeek = !empty($row->date) ? date('l', strtotime($row->date)) : '';
                return '
                    <div>
                        <span class="fw-bold text-dark font-14">' . $dateFormatted . '</span>
                        <small class="text-muted d-block font-11">' . $dayOfWeek . '</small>
                    </div>';
            })
            ->addColumn('delivered_parcels', function ($row) {
                return '<span class="fw-semibold text-dark font-14">' . number_format($row->delivered_count ?? 0) . '</span>';
            })
            ->addColumn('cod_collected', function ($row) {
                return '<span class="fw-semibold font-14 text-dark">' . format_price($row->cod_collected ?? 0) . '</span>';
            })
            ->addColumn('delivery_charge', function ($row) {
                return '<span class="badge rounded-2 px-2 py-1 fw-bold font-13 text-success" style="background: #dcfce7;">' . format_price($row->delivery_charge ?? 0) . ' <i class="las la-arrow-up"></i></span>';
            })
            ->addColumn('return_charge', function ($row) {
                return '<span class="badge rounded-2 px-2 py-1 fw-bold font-13 text-warning" style="background: #fef3c7; color: #b45309 !important;">' . format_price($row->return_charge ?? 0) . ' <i class="las la-arrow-down"></i></span>';
            })
            ->addColumn('rider_commission', function ($row) {
                return '<span class="fw-semibold font-14" style="color: #475569;">' . format_price($row->rider_commission ?? 0) . '</span>';
            })
            ->addColumn('expense', function ($row) {
                return '<span class="badge rounded-2 px-2 py-1 fw-bold font-13 text-danger" style="background: #fee2e2; color: #b91c1c !important;">' . format_price($row->expense ?? 0) . ' <i class="las la-arrow-down"></i></span>';
            })
            ->addColumn('net_profit', function ($row) {
                $revenue = (float) ($row->delivery_charge ?? 0) + (float) ($row->return_charge ?? 0);
                $costs   = (float) ($row->rider_commission ?? 0) + (float) ($row->expense ?? 0);
                $profit  = $revenue - $costs;

                if ($profit >= 0) {
                    return '<span class="badge rounded-2 px-2 py-1 fw-bold font-13 text-success" style="background: #d1fae5; color: #065f46 !important;">' . format_price($profit) . ' <i class="las la-arrow-up"></i></span>';
                } else {
                    return '<span class="badge rounded-2 px-2 py-1 fw-bold font-13 text-danger" style="background: #fee2e2; color: #991b1b !important;">' . format_price($profit) . ' <i class="las la-arrow-down"></i></span>';
                }
            })
            ->rawColumns(['date', 'delivered_parcels', 'cod_collected', 'delivery_charge', 'return_charge', 'rider_commission', 'expense', 'net_profit'])
            ->setRowId('date');
    }

    public function query(): QueryBuilder
    {
        $startDate = $this->request->get('from_date');
        $endDate   = $this->request->get('to_date');

        $query = Parcel::query()
            ->select('date')
            ->selectRaw("COUNT(CASE WHEN status IN ('delivered', 'delivered-and-verified', 'partially-delivered') THEN 1 END) as delivered_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('delivered', 'delivered-and-verified', 'partially-delivered') THEN price ELSE 0 END), 0) as cod_collected")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('delivered', 'delivered-and-verified', 'partially-delivered') THEN total_delivery_charge ELSE 0 END), 0) as delivery_charge")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant', 'cancel', 'cancelled') THEN return_charge ELSE 0 END), 0) as return_charge")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('delivered', 'delivered-and-verified', 'partially-delivered') THEN delivery_fee ELSE 0 END + CASE WHEN status IN ('returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant', 'cancel', 'cancelled') THEN return_fee ELSE 0 END), 0) as rider_commission")
            ->selectSub(function ($q) {
                $q->from('company_accounts')
                  ->whereColumn('company_accounts.date', 'parcels.date')
                  ->where('company_accounts.type', 'expense')
                  ->where('company_accounts.create_type', 'user_defined')
                  ->selectRaw('COALESCE(SUM(amount), 0)');
            }, 'expense')
            ->whereNotNull('date');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        return $query->groupBy('date')->orderByDesc('date');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
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
            Column::make('date')->title(__('date'))->addClass('ps-3'),
            Column::computed('delivered_parcels')->title(__('delivered_parcels'))->addClass('text-center'),
            Column::computed('cod_collected')->title(__('cod_collected'))->addClass('text-center'),
            Column::computed('delivery_charge')->title(__('delivery_charge'))->addClass('text-center'),
            Column::computed('return_charge')->title(__('return_charge'))->addClass('text-center'),
            Column::computed('rider_commission')->title(__('rider_commission'))->addClass('text-center'),
            Column::computed('expense')->title(__('expense'))->addClass('text-center'),
            Column::computed('net_profit')->title(__('net_profit'))->addClass('pe-3 text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'FinancialReport_' . date('YmdHis');
    }
}
