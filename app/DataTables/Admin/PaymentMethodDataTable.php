<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Yajra\DataTables\Services\DataTable;

class PaymentMethodDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($payment) {
            return view('admin.payment-method.column.options', compact('payment'));
        })->addColumn('name', function ($payment) {
            return view('admin.payment-method.column.name', compact('payment'));
        })->addColumn('image', function ($payment) {
            return view('admin.payment-method.column.image', compact('payment'));
        })->addColumn('type', function ($payment) {
            return view('admin.payment-method.column.type', compact('payment'));
        })->addColumn('status', function ($payment) {
            return view('admin.payment-method.column.status', compact('payment'));
        })->setRowId('id');
    }
    public function query(PaymentMethod $model): QueryBuilder
    {
        $query = $model->latest();

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
                    'lengthMenu'        => '_MENU_ '.__('payout_method_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('name')->title(__('name')),
            Column::computed('image')->title(__('image')),
            Column::computed('type')->title(__('type')),
            Column::computed('status')->title(__('status'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'payment_method'.date('YmdHis');
    }
}
