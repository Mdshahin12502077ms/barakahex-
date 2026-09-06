<?php

namespace App\DataTables\Admin;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use App\Models\Account\CompanyAccount;
use Yajra\DataTables\Services\DataTable;

class IncomeDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($income) {
            return view('admin.accounts.column.options', compact('income'));
        })->addColumn('created_at', function ($income) {
            return view('admin.accounts.column.created_at', compact('income'));
        })->addColumn('account_details', function ($income) {
            return view('admin.accounts.column.account_details', compact('income'));
        })->addColumn('source_details', function ($income) {
            return view('admin.accounts.column.source_details', compact('income'));
        })->addColumn('parcel', function ($income) {
            return view('admin.accounts.column.parcel', compact('income'));
        })->addColumn('amount', function ($income) {
            return view('admin.accounts.column.amount', compact('income'));
        })->setRowId('id');
    }
    public function query(CompanyAccount $model): QueryBuilder
    {
        $query = $model->where('type', 'income')
                       ->where('create_type', 'user_defined')
                       ->when(!hasPermission('read_all_income'), function ($q) {
                           $q->where('created_by', \Sentinel::getUser()->id);
                       });

            $query->when(request('search')['value'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('account', function ($q) use ($search) {
                        $q->where('account_holder_name', 'like', "%$search%")
                            ->orWhere('method', 'like', "%$search%")
                            ->orWhere('account_no', 'like', "%$search%")
                            ->orWhere('number', 'like', "%$search%")
                            ->orWhere('bank_name', 'like', "%$search%");
                    });
                    $query->orWhereHas('parcel', function ($q) use ($search) {
                        $q->where('parcel_no', 'like', "%$search%");
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
                    'lengthMenu'        => '_MENU_ '.__('income_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('created_at')->title(__('created_at')),
            Column::computed('account_details')->title(__('account_details')),
            Column::computed('source_details')->title(__('source_details')),
            Column::computed('parcel')->title(__('parcel')),
            Column::computed('amount')->title(__('amount'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'income'.date('YmdHis');
    }
}
