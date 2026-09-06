<?php

namespace App\DataTables\Admin;

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
        })->addColumn('payout', function ($payment) {
            return view('admin.withdraws.column.payment', compact('payment'));
        })->addColumn('account_details', function ($payment) {
            return view('admin.withdraws.column.account_details', compact('payment'));
        })->addColumn('status', function ($payment) {
            return view('admin.withdraws.column.status', compact('payment'));
        })->addColumn('amount', function ($payment) {
            return view('admin.withdraws.column.amount', compact('payment'));
        })
        ->setRowId('id');
    }
    public function query(MerchantWithdraw $model): QueryBuilder
    {
        $user = \Sentinel::getUser();

        $query = $model->with('payments');

        if (!hasPermission('read_all_withdraw')) {
            $query->whereHas('user', function (Builder $q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        $query->when($this->request->has('status') && $this->request->status !== "", function ($query) {
            $status = $this->request->status;
            $query->where('status', $status);
        });

        $query->when($this->request->has('merchant') && $this->request->merchant !== "", function ($query) {
            $merchant = $this->request->merchant;
            $query->whereHas('merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant);
            });
        });

        if ($search = $this->request->input('search.value')) {
            $query->where(function ($query) use ($search) {
                $query->where('withdraw_id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhereHas('merchant', function ($q) use ($search) {
                        $q->where('company', 'like', "%$search%");
                    });
            });
        }

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
                    'lengthMenu'        => '_MENU_ '.__('payout_per_page'),
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
            Column::computed('payout')->title(__('payout')),
            Column::computed('account_details')->title(__('account_details')),
            Column::computed('status')->title(__('status')),
            Column::computed('amount')->title(__('amount')),


        ];
    }

    protected function filename(): string
    {
        return 'payment'.date('YmdHis');
    }
}
