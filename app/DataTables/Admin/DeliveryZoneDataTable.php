<?php

namespace App\DataTables\Admin;

use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DeliveryZoneDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('thana', function ($deliveryZone) {
                return $deliveryZone->thana->name ?? 'N/A';
            })
            ->addColumn('status', function ($deliveryZone) {
                return view('admin.delivery_zone.status', compact('deliveryZone'));
            })
            ->addColumn('action', function ($deliveryZone) {
                return view('admin.delivery_zone.action', compact('deliveryZone'));
            })
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return DeliveryZone::with('thana')->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->parameters([
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ ' . __('delivery_zone_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::make('name')->title(__('name')),
            Column::computed('thana')->title(__('thana')),
            Column::computed('status')->title(__('status'))->exportable(false)->printable(false)->width(10),
            Column::computed('action')->title(__('action'))->exportable(false)->printable(false)->searchable(false)->addClass('action-card')->width(10),
        ];
    }

    protected function filename(): string
    {
        return 'DeliveryZone_' . date('YmdHis');
    }
}
