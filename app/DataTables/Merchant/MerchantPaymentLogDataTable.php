<?php

namespace App\DataTables\Merchant;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use App\Models\Account\MerchantAccount;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class MerchantPaymentLogDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('details', function ($query) {
            return view('merchant.profile.column.details', compact('query'));
        })
        ->addColumn('created_at', function ($query) {
            return $query->created_at ? date('M d, Y h:i a', strtotime($query->created_at)) : '';
        })
        ->addColumn('amount', function ($query) {
            return view('merchant.profile.column.amount', compact('query'));
        })
        ->setRowId('id');
    }

    public function query(MerchantAccount $model): QueryBuilder
    {
        $query = $model->where('source', '!=', 'paid_parcels_delivery_reverse');
        if (Sentinel::getUser()->user_type == 'merchant_staff') {
            $query->whereNotIn('source',['previous_balance','cash_given_for_delivery_charge','opening_balance']);
            if (!hasPermission('all_parcel_logs') && !hasPermission('all_payment_logs')){
                $query->whereHas('parcel',function ($q){
                    $q->where('user_id', Sentinel::getUser()->id);
                })->orWhereHas('withdraw', function ($q){
                    $q->where('created_by', Sentinel::getUser()->id);
                });
            }elseif (!hasPermission('all_parcel_logs')){
                $query->whereHas('parcel',function ($q){
                    $q->where('user_id', Sentinel::getUser()->id);
                })->orWhereHas('withdraw');
            } elseif (!hasPermission('all_payment_logs')){
                $query->whereHas('withdraw', function ($q){
                    $q->where('created_by', Sentinel::getUser()->id);
                })->orWhereHas('parcel');
            }
        }

        if (Sentinel::getUser()->user_type == 'merchant') {

            $merchant_id = Sentinel::getUser()->merchant->id;
            $query->where('merchant_id',$merchant_id);
        }

        $query->latest();
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
            ->footerCallback('function (row, data, start, end, display ) {
                $(".dataTables_length select").addClass("without_search mb-3");
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
                    'lengthMenu'        => '_MENU_ '.__('payout_log_per_page'),
                    'search'            => '',
                ],
            ]);
    }


    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('details')->title(__('details')),
            Column::computed('created_at')->addClass('text-center')->title(__('created_at')),
            Column::computed('amount')->addClass('text-end')->title(__('amount')),
        ];
    }

    protected function filename(): string
    {
        return 'statements_'.date('YmdHis');
    }
}
