<?php

namespace App\DataTables\Admin;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($query) {
                return view('admin.roles.column.actions', compact('query'));
            })
            ->addColumn('role', fn($query) => $query->name)
            ->addColumn('permissions', fn($query) => @count($query->permissions) ?? 0)
            ->addColumn('status', function ($query) {
                return view('admin.roles.column.status', compact('query'));
            })
            ->setRowId('id');
    }

    public function query(Role $model): QueryBuilder
    {

        return $model
        ->when(request('order')[0]['dir'] ?? false, function ($query, $orderBy) {
            $query->orderBy('id', $orderBy);
        })
        ->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        })
        ->latest()->newQuery();
    }
    public function getTotalCount(): int
    {
        return Role::count();
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function (row, data, start, end, display ) {
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
                    'lengthMenu'        => '_MENU_ '.__('role_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('action')
            ->title(__('action'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),
            Column::make('role')->title(__('role'))->addClass('text-center'),
            Column::make('permissions')->title(__('permissions'))
            ->addClass('text-center')
            ->exportable(false)
            ->printable(false)
            ->width(60),
            Column::computed('status')->title(__('status'))->addClass('text-center')
            ->exportable(false)
            ->printable(false)
            ->width(60),

        ];
    }

    protected function filename(): string
    {
        return 'role_'.date('YmdHis');
    }
}
