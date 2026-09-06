<?php

namespace App\DataTables\Admin;

use App\Models\Bag;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BagDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('from_branch', function ($bag) {
                return $bag->fromBranch->name ?? 'N/A';
            })
            ->addColumn('to_branch', function ($bag) {
                return $bag->toBranch->name ?? 'N/A';
            })
            ->addColumn('status', function ($bag) {
                return view('admin.bag.status', compact('bag'));
            })
            ->addColumn('action', function ($bag) {
                return view('admin.bag.action', compact('bag'));
            })
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return Bag::with(['fromBranch', 'toBranch', 'creator'])->latest();
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
                    'lengthMenu'        => '_MENU_ ' . __('bag_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(__('sl'))->searchable(false)->orderable(false)->width(10)->addClass('text-center'),
            Column::make('bag_no')->title(__('bag_no')),
            Column::computed('from_branch')->title(__('from_branch')),
            Column::computed('to_branch')->title(__('to_branch')),
            Column::make('total_parcels')->title(__('total_parcels')),
            Column::computed('status')->title(__('status'))->addClass('text-center'),
            Column::computed('action')->title(__('action'))
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Bag_' . date('YmdHis');
    }
}
