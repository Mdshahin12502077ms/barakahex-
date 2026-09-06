<?php

namespace App\DataTables\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StaffDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->addColumn('action', function ($query) {
                return view('admin.users.column.options', compact('query'));
            })

            ->addColumn('user_info', function ($query) {
                return view('admin.users.column.user_info', compact('query'));
            })
            ->addColumn('branch', function ($query) {
                return view('admin.users.column.branch', compact('query'));
            })
            ->addColumn('last_login', fn($query) => $query->last_login)
            ->addColumn('status', function ($query) {
                return view('admin.users.column.status', compact('query'));
            })
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->where('id', '!=', \Sentinel::getUser()->id)->where('id', '!=', '1')->where('user_type', 'staff')
        ->when(request('order')[0]['dir'] ?? false, function ($query, $orderBy) {
            $query->orderBy('id', $orderBy);
        })
        ->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        })
        ->latest()->newQuery();
    }
    public function getTotalCount(): int
    {
        return User::where('id', '!=', \Sentinel::getUser()->id)->where('id', '!=', '1')->where('user_type', 'staff')->count();
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
                    'lengthMenu'        => '_MENU_ '.__('staff_per_page'),
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
            Column::make('user_info')->title(__('user_info')),
            Column::make('branch')->title(__('branch')),
            Column::make('last_login')->title(__('last_login')),
            Column::computed('status')->title(__('status'))
            ->exportable(false)
            ->printable(false)
            ->width(60),

        ];
    }

    protected function filename(): string
    {
        return 'user_'.date('YmdHis');
    }
}
