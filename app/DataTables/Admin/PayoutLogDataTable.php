<?php

namespace App\DataTables\Admin;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use App\Models\Account\CompanyAccount;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PayoutLogDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('details', function ($statement) {
            return view('admin.users.details.payment-logs.column.details', compact('statement'));
        })
        ->addColumn('source', function ($statement) {
            return view('admin.users.details.payment-logs.column.source', compact('statement'));
        })
        ->addColumn('completed_at', function ($statement) {
            return view('admin.users.details.payment-logs.column.completed_at', compact('statement'));
        })
        ->addColumn('amount', function ($statement) {
            return view('admin.users.details.payment-logs.column.amount', compact('statement'));
        })
        ->setRowId('id');
    }

    public function query(CompanyAccount $model): QueryBuilder
    {

        $query = CompanyAccount::orderby('id', 'desc')->where('user_id', Sentinel::getUser()->id);

        return $query->latest()->newQuery();
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
                    'lengthMenu'        => '_MENU_ '.__('transaction_log_per_page'),
                    'search'            => '',
                ],
            ]);
    }


    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('details')->title(__('details')),
            Column::computed('source')->title(__('source')),
            Column::computed('completed_at')->title(__('completed_at')),
            Column::computed('amount')->title(__('amount')),
        ];
    }

    protected function filename(): string
    {
        return 'statements_'.date('YmdHis');
    }
}
