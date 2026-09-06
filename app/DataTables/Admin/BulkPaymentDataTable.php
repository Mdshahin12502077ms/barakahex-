<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\WithdrawBatch;
use App\Enums\PaymentMethodType;
use Yajra\DataTables\Services\DataTable;

class BulkPaymentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.options', compact('bulk_payment'));
        })->addColumn('batch_no', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.batch', compact('bulk_payment'));
        })->addColumn('title', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.title', compact('bulk_payment'));
        })->addColumn('account', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.account', compact('bulk_payment'));
        })->addColumn('request', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.request', compact('bulk_payment'));
        })->addColumn('amount', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.amount', compact('bulk_payment'));
        })->addColumn('status', function ($bulk_payment) {
            return view('admin.withdraws.bulk.column.status', compact('bulk_payment'));
        })->setRowId('id');
    }
    public function query(WithdrawBatch $model): QueryBuilder
    {
        $query = $model->whereHas('user', function(Builder $q) {
                    if (!hasPermission('read_all_withdraw')) {
                        $q->where('branch_id', \Sentinel::getUser()->branch_id);
                    }
                })->with(['account', 'user', 'withdraws.payments.paymentAccount']);

        $query->when(request('search.value'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('batch_no', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('title', 'like', "%$search%");
            });
        })->latest()
        ->newQuery();

        return $query;
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
                    'lengthMenu'        => '_MENU_ '.__('bulk_payout_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('batch_no')->title(__('batch_no')),
            Column::computed('title')->title(__('title')),
            Column::computed('account')->title(__('account')),
            Column::computed('request')->title(__('request')),
            Column::computed('amount')->title(__('amount')),
            Column::computed('status')->title(__('status'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'bulk_payment'.date('YmdHis');
    }
}
