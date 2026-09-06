<?php

namespace App\DataTables\Admin;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use App\Models\Branch;
use Yajra\DataTables\Services\DataTable;

class BranchDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($query) {
                return view('admin.branch.column.options', compact('query'));
            })->addColumn('manager', function ($query) {
                return view('admin.branch.column.charge', compact('query'));
            })->addColumn('branch', function ($query) {
                return view('admin.branch.column.name', compact('query'));
            })->addColumn('address', function ($query) {
                return view('admin.branch.column.address', compact('query'));
            })
            ->addColumn('status', function ($query) {
                return view('admin.branch.column.status', compact('query'));
            })
            ->addColumn('default', function ($query) {
                return view('admin.branch.column.default', compact('query'));
            })
            ->setRowId('id');
    }
    public function query(Branch $model): QueryBuilder
    {
        $query = $model::with(['user', 'parent']);

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%")
                    ->orWhereHas('parent', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        });
        return $query->latest();
    }

    public function html()
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
                'dom' => 'Blfrtip',
                'buttons' => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language' => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu' => '_MENU_ ' . __('branch_per_page'),
                    'search' => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center')->exportable(false)
                ->printable(false)
                ->width(60),
            Column::computed('manager')->title(__('manager')),
            Column::computed('branch')->title(__('branch')),
            Column::computed('address')->title(__('address'))
                ->exportable(false)->addClass('text-start')
                ->printable(false)
                ->width(60),
            Column::computed('status')->title(__('status'))
                ->exportable(false)
                ->printable(false)
                ->width(60),
            Column::computed('default')->title(__('default'))
                ->exportable(false)
                ->printable(false)
                ->width(60),

        ];
    }

    protected function filename(): string
    {
        return 'account' . date('YmdHis');
    }
}
