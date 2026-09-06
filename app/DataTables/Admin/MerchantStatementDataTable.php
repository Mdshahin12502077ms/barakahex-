<?php

namespace App\DataTables\Admin;

use App\Models\Merchant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Yajra\DataTables\Services\DataTable;

class MerchantStatementDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('details', function ($statement) {
            return view('admin.merchants.details.statement.column.details', compact('statement'));
        })->addColumn('source', function ($statement) {
            return view('admin.merchants.details.statement.column.source', compact('statement'));
        })->addColumn('completed_at', function ($statement) {
            return view('admin.merchants.details.statement.column.completed_at', compact('statement'));
        })->addColumn('amount', function ($statement) {
            return view('admin.merchants.details.statement.column.amount', compact('statement'));
        })->setRowId('id');
    }


    public function query(User $model): Builder
    {

        $merchant       = Merchant::where('id', $this->id)->first();
        $branch_match   = $merchant->withPermission($merchant->id)->get();


        if(hasPermission('read_all_merchant') || $branch_match || $branch_match == ''):
            return $merchant->accountStatements()->getQuery();
        else:
            return back()->with('danger', __('access_denied'));
        endif;
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
            Column::computed('source')->title(__('source')),
            Column::computed('completed_at')->title(__('completed_at')),
            Column::computed('amount')->title(__('amount'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'merchant_statement_'.date('YmdHis');
    }
}
