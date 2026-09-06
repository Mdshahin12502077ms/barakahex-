<?php

namespace App\DataTables\Admin;

use App\Models\Area;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AreaDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('delivery_zone', function ($area) {
                return $area->deliveryZone->name ?? 'N/A';
            })
            ->addColumn('thana', function ($area) {
                return $area->thana->name ?? ($area->deliveryZone->thana->name ?? 'N/A');
            })
            ->addColumn('district', function ($area) {
                return $area->district->name ?? ($area->deliveryZone->thana->district->name ?? 'N/A');
            })
            ->addColumn('status', function ($area) {
                return view('admin.area.status', compact('area'));
            })
            ->addColumn('action', function ($area) {
                return view('admin.area.action', compact('area'));
            })
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return Area::with(['deliveryZone.thana.district.division', 'thana', 'district', 'division'])->latest();
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
                    'lengthMenu'        => '_MENU_ ' . __('area_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::make('name')->title(__('name')),
            Column::computed('delivery_zone')->title(__('delivery_zone')),
            Column::computed('thana')->title(__('thana')),
            Column::computed('district')->title(__('district')),
            Column::computed('status')->title(__('status'))->exportable(false)->printable(false)->width(10),
            Column::computed('action')->title(__('action'))->exportable(false)->printable(false)->searchable(false)->addClass('action-card')->width(10),
        ];
    }

    protected function filename(): string
    {
        return 'Area_' . date('YmdHis');
    }
}
