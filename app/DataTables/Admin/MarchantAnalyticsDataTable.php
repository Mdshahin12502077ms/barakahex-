<?php

namespace App\DataTables\Admin;

use App\Models\Merchant;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class MarchantAnalyticsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('return_rate', function ($row) {
                if ($row->total_orders > 0) {
                    $rate = ($row->returns / $row->total_orders) * 100;
                    return number_format($rate, 2) . '%';
                }
                return '0.00%';
            })
            ->editColumn('total_revenue', function ($row) {
                return '$' . number_format($row->total_revenue, 2);
            })
            ->editColumn('cod_amount', function ($row) {
                return '$' . number_format($row->cod_amount, 2);
            })
            ->editColumn('settlement', function ($row) {
                return '$' . number_format($row->settlement, 2);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Merchant $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Merchant $model): QueryBuilder
    {
        $startDate = request()->get('start_date');
        $endDate = request()->get('end_date');

        $query = $model->newQuery();

        $dateFilter = function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
        };

        $query->withCount(['parcels as total_orders' => $dateFilter])
            ->withCount(['parcels as delivered' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['delivered', 'delivered-and-verified']);
                if ($startDate && $endDate) {
                    $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }])
            ->withCount(['parcels as returns' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['returned', 'return-to-merchant', 'return-to-merchant-and-verified']);
                if ($startDate && $endDate) {
                    $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }])
            ->withSum(['parcels as total_revenue' => $dateFilter], 'price')
            ->withSum(['parcels as cod_amount' => $dateFilter], 'price') // Assuming COD is based on price
            ->withSum(['parcels as settlement' => $dateFilter], 'payable');

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('merchantanalyticsdatatable-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax('', '
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                    ')
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->parameters([
                        'destroy' => true,
                    ])
                    ->buttons(
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('company')->title('Merchant Name'),
            Column::make('total_orders')->title('Total Orders')->searchable(false),
            Column::make('delivered')->title('Delivered')->searchable(false),
            Column::make('returns')->title('Returns')->searchable(false),
            Column::computed('return_rate')->title('Return Rate (%)')->searchable(false)->orderable(false),
            Column::make('total_revenue')->title('Total Revenue ($)')->searchable(false),
            Column::make('cod_amount')->title('COD Amount ($)')->searchable(false),
            Column::make('settlement')->title('Settlement ($)')->searchable(false),
        ];
    }
}
