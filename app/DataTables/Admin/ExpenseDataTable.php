<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Account\CompanyAccount;
use Yajra\DataTables\Services\DataTable;

class ExpenseDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($expense) {
            return view('admin.accounts.expenses.column.options', compact('expense'));
        })->addColumn('title_details', function ($expense) {
            return view('admin.accounts.expenses.column.title', compact('expense'));
        })->addColumn('account_details', function ($expense) {
            return view('admin.accounts.expenses.column.account_details', compact('expense'));
        })->addColumn('date_created_at', function ($expense) {
            return view('admin.accounts.expenses.column.created_at', compact('expense'));
        })->addColumn('amount', function ($expense) {
            return view('admin.accounts.expenses.column.amount', compact('expense'));
        })->addColumn('receipt', function ($expense) {
            return view('admin.accounts.expenses.column.receipt', compact('expense'));
        })->setRowId('id');
    }
    public function query(CompanyAccount $model): QueryBuilder
    {
        $query = $model->where('type', 'expense')
                    ->where('create_type', 'user_defined')
                    ->when(!hasPermission('read_all_expense'), function ($q) {
                        $q->where('created_by', \Sentinel::getUser()->id);
                    });

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('details', 'like', "%$search%")
                    ->orWhereHas('account', function ($q) use ($search) {
                        $q->where('account_holder_name', 'like', "%$search%")
                            ->orWhere('method', 'like', "%$search%")
                            ->orWhere('account_no', 'like', "%$search%")
                            ->orWhere('number', 'like', "%$search%")
                            ->orWhere('bank_name', 'like', "%$search%");
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
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ '.__('expense_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('title_details')->title(__('title_details')),
            Column::computed('account_details')->title(__('account_details')),
            Column::computed('date_created_at')->title(__('date_created_at')),
            Column::computed('amount')->title(__('amount')),
            Column::computed('receipt')->title(__('receipt'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'expense'.date('YmdHis');
    }
}
