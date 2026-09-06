<?php

namespace App\DataTables\Admin\Profile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Account\MerchantWithdraw;
use Yajra\DataTables\Services\DataTable;

class PaymentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($payment) {
            return view('admin.withdraws.column.options', compact('payment'));
        })->addColumn('merchant', function ($payment) {
            return view('admin.withdraws.column.merchant', compact('payment'));
        })->addColumn('payment', function ($payment) {
            return view('admin.withdraws.column.payment', compact('payment'));
        })->addColumn('account_details', function ($payment) {
            return view('admin.withdraws.column.account_details', compact('payment'));
        })->addColumn('requested_at', function ($payment) {
            return view('admin.withdraws.column.requested_at', compact('payment'));
        })->addColumn('status', function ($payment) {
            return view('admin.withdraws.column.status', compact('payment'));
        })->addColumn('amount', function ($payment) {
            return view('admin.withdraws.column.amount', compact('payment'));
        })->addColumn('receipt', function ($payment) {
            return view('admin.withdraws.column.receipt', compact('payment'));
        })->setRowId('id');
    }
    public function query(MerchantWithdraw $model): QueryBuilder
    {
        $query = $model->query();

        $query->when($this->request->has('status') && $this->request->status !== "", function ($query) {
                $status = $this->request->status;
                $query->where('status', $status);
                })
                ->when($this->request->has('merchant') && $this->request->merchant !== "", function ($query) {
                    $merchant = $this->request->merchant;
                    $query->whereHas('merchant', function ($q) use ($merchant) {
                        $q->where('id', $merchant);
                    });
                });
        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
            $query->where('withdraw_id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhereHas('merchant', function ($q) use ($search) {
                        $q->where('company', 'like', "%$search%");
                    });
            });
        });

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
                    'lengthMenu'        => '_MENU_ '.__('payment_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('merchant')->title(__('merchant')),
            Column::computed('payment')->title(__('payment')),
            Column::computed('account_details')->title(__('account_details')),
            Column::computed('requested_at')->title(__('requested_at')),
            Column::computed('status')->title(__('status')),
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
        return 'payment'.date('YmdHis');
    }
}
