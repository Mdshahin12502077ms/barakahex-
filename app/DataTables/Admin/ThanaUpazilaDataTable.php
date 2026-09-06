<?php

namespace App\DataTables\Admin;

use App\Models\Thana;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ThanaUpazilaDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('district', function ($upazila) {
                return $upazila->district->name ?? 'N/A';
            })
            ->addColumn('status', function ($upazila) {
                return view('admin.upazila.status', compact('upazila'));
            })
            ->addColumn('action', function ($upazila) {
                return view('admin.upazila.action', compact('upazila'));
            })
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return Thana::with('district')->latest();
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
                    'lengthMenu'        => '_MENU_ ' . __('upazila_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::make('name')->title(__('name')),
            Column::computed('district')->title(__('district')),
            Column::computed('status')->title(__('status'))->exportable(false)->printable(false)->width(10),
            Column::computed('action')->title(__('action'))->exportable(false)->printable(false)->searchable(false)->addClass('action-card')->width(10),
        ];
    }

    protected function filename(): string
    {
        return 'Upazila_' . date('YmdHis');
    }
}

