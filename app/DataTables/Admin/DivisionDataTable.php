<?php

namespace App\DataTables\Admin;

use App\Models\Division;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DivisionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('country', function ($division) {
                return $division->country->name ?? 'N/A';
            })
            ->addColumn('status', function ($division) {
                return view('admin.division.status', compact('division'));
            })
            ->addColumn('action', function ($division) {
                return view('admin.division.action', compact('division'));
            })
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return Division::with('country')->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function ( row, data, start, end, display ) {
                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
            ->parameters([
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ '.__('division_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::make('name')->title(__('name')),
            Column::computed('country')->title(__('country')),
            Column::computed('status')->title(__('status'))->exportable(false)->printable(false)->width(10),
            Column::computed('action')->title(__('action'))->exportable(false)->printable(false)->searchable(false)->addClass('action-card')->width(10),
        ];
    }

    protected function filename(): string
    {
        return 'Division_'.date('YmdHis');
    }
}
