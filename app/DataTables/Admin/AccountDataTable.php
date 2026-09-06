<?php

namespace App\DataTables\Admin;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use App\Models\Account\Account;
use Yajra\DataTables\Services\DataTable;

class AccountDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($account) {
                return view('admin.accounts.accounts.column.options', compact('account'));
            })->addColumn('staff_account_details', function ($account) {
                return view('admin.accounts.accounts.column.account_details', compact('account'));
            })->addColumn('method', function ($account) {
                return view('admin.accounts.accounts.column.method', compact('account'));
            })->addColumn('opening_balance_tk', function ($account) {
                return view('admin.accounts.accounts.column.opening_balance', compact('account'));
            })->addColumn('current_balance_tk', function ($account) {
                return view('admin.accounts.accounts.column.current_balance', compact('account'));
            })->setRowId('id');
    }
    public function query(Account $model): QueryBuilder
    {
        $query = $model::when(!hasPermission('read_all_account'), function ($q) {
            $q->where('created_by', \Sentinel::getUser()->id);
        });

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('account_holder_name', 'like', "%$search%")
                    ->orWhere('method', 'like', "%$search%")
                    ->orWhere('account_no', 'like', "%$search%")
                    ->orWhere('number', 'like', "%$search%")
                    ->orWhere('bank_name', 'like', "%$search%")
                    ->orWhere('bank_branch', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%");
            });
        })->latest();
        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
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
                    'lengthMenu' => '_MENU_ ' . __('account_per_page'),
                    'search' => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('staff_account_details')->title(__('staff_account_details')),
            Column::computed('method')->title(__('method')),
            Column::computed('opening_balance_tk')->title(__('opening_balance_tk')),
            Column::computed('current_balance_tk')->title(__('current_balance_tk'))
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
