<?php

namespace App\DataTables\Merchant;

use App\Models\Account\MerchantWithdraw;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Yajra\DataTables\Services\DataTable;

class MerchantPaymentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('payment_id', function ($withdraw) {
            return view('merchant.withdraw.column.payment_id', compact('withdraw'));
        })->addColumn('account_details', function ($withdraw) {
            return view('merchant.withdraw.column.account_details', compact('withdraw'));
        })->addColumn('requested_at', function ($withdraw) {
            return view('merchant.withdraw.column.requested_at', compact('withdraw'));
        })->addColumn('status', function ($withdraw) {
            return view('merchant.withdraw.column.status', compact('withdraw'));
        })->addColumn('amount', function ($withdraw) {
            return view('merchant.withdraw.column.amount', compact('withdraw'));
        })->addColumn('options', function ($withdraw) {
            return view('merchant.withdraw.column.action', compact('withdraw'));
        })->setRowId('id');
    }

    public function query(MerchantWithdraw $model): QueryBuilder
    {

        $query = $model->query();

        if (Sentinel::getUser()->user_type == 'merchant_staff') {
            $query->where('merchant_id', Sentinel::getUser()->merchant_id)
                ->when(!hasPermission('all_parcel_payment'), function ($query) {
                    $query->whereHas('companyAccount', function ($q) {
                        $q->where('created_by', Sentinel::getUser()->id);
                    });
                });
        }

        if (Sentinel::getUser()->user_type == 'merchant') {
            $query->where('merchant_id', Sentinel::getUser()->merchant->id);
        }
        $query->when($this->request->search['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('withdraw_id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        })
        ->latest();
        return $query->newQuery();

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
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false),
            Column::computed('payment_id')->title(__('payment_id'))->addClass('text-center'),
            Column::computed('account_details')->title(__('account_details')),
            Column::computed('requested_at')->title(__('requested_at')),
            Column::computed('status')->title(__('status')),
            Column::computed('amount')->title(__('amount')),
            Column::computed('options')->title(__('options'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'parcel'.date('YmdHis');
    }
}
