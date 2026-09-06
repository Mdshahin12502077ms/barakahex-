<?php

namespace App\DataTables\Admin;

use App\Models\District;
use App\Models\DistrictZila;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DistrictDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('division', function ($district) {
                return $district->division->name ?? 'N/A';
            })
            ->addColumn('status', function ($district) {
                return view('admin.district.status', compact('district'));
            })
            ->addColumn('action', function ($district) {
                return view('admin.district.action', compact('district'));
            })
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return District::with('division')->latest();
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
                    'lengthMenu'        => '_MENU_ ' . __('district_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::make('name')->title(__('name')),
            Column::computed('division')->title(__('Division')),
            Column::computed('status')->title(__('status'))->exportable(false)->printable(false)->width(10),
            Column::computed('action')->title(__('action'))->exportable(false)->printable(false)->searchable(false)->addClass('action-card')->width(10),
        ];
    }

    protected function filename(): string
    {
        return 'District_' . date('YmdHis');
    }
}
