<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Account\FundTransfer;
use Yajra\DataTables\Services\DataTable;

class FundTransferDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($fund_transfer) {
            return view('admin.accounts.fund-transfer.column.options', compact('fund_transfer'));
        })->addColumn('from', function ($fund_transfer) {
            return view('admin.accounts.fund-transfer.column.from', compact('fund_transfer'));
        })->addColumn('to', function ($fund_transfer) {
            return view('admin.accounts.fund-transfer.column.to', compact('fund_transfer'));
        })->addColumn('date', function ($fund_transfer) {
            return view('admin.accounts.fund-transfer.column.date', compact('fund_transfer'));
        })->addColumn('details', function ($fund_transfer) {
            return view('admin.accounts.fund-transfer.column.details', compact('fund_transfer'));
        })->addColumn('amount', function ($fund_transfer) {
            return view('admin.accounts.fund-transfer.column.amount', compact('fund_transfer'));
        })->setRowId('id');
    }
    public function query(FundTransfer $model): QueryBuilder
    {

        $query = $model->where(function($query) {
                    if (!hasPermission('read_all_fund_transfer')) {
                        $query->where(function($q) {
                            $q->whereHas('fromAccount', function($q) {
                                $q->where('user_id', \Sentinel::getUser()->id);
                            })->orWhereHas('toAccount', function($q) {
                                $q->where('user_id', \Sentinel::getUser()->id);
                            });
                        });
                    }
                });


        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('fromAccount', function ($q) use ($search) {
                    $q->where('account_holder_name', 'like', "%$search%")
                        ->orWhere('method', 'like', "%$search%")
                        ->orWhere('account_no', 'like', "%$search%")
                        ->orWhere('number', 'like', "%$search%")
                        ->orWhere('bank_name', 'like', "%$search%");
                });

                $query->orWhereHas('toAccount', function ($q) use ($search) {
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
            // ->orderBy(1)
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
                    'lengthMenu'        => '_MENU_ '.__('fund_transfer_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('from')->title(__('from')),
            Column::computed('to')->title(__('to')),
            Column::computed('date')->title(__('date')),
            Column::computed('details')->title(__('details')),
            Column::computed('amount')->title(__('amount'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'fund_transfer'.date('YmdHis');
    }
}
