<?php

namespace App\DataTables\Admin;

use App\Models\WebsiteStatistic;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WebsiteStatisticDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($statistic) {
                return view('admin.website.statistic.action', compact('statistic'));
            })->addColumn('status', function ($statistic) {
                return view('admin.website.statistic.status', compact('statistic'));
            })->addColumn('sub_title', function ($statistic) {
                return @$statistic->language->sub_title;
            })->addColumn('title', function ($statistic) {
                return @$statistic->language->title;
            })->addColumn('number', function ($statistic) {
                return @$statistic->language->number;
            })->addColumn('icon', function ($statistic) {
                return view('admin.website.statistic.icon', compact('statistic'));
            })->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        $model = WebsiteStatistic::with('language');

        return $model->latest()->newQuery();
    }

    public function html(): HtmlBuilder
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
                    'lengthMenu'        => '_MENU_ '.__('statistic_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options')),
            Column::computed('icon')->title(__('icon')),
            Column::make('number')->title(__('number')),
            Column::make('title')->title(__('title')),
            Column::make('sub_title')->title(__('sub_title')),
            Column::computed('status')->title(__('status'))->exportable(false)
                ->printable(false)
                ->searchable(false)->addClass('action-card text-end')->width(10),

        ];
    }

    protected function filename(): string
    {
        return 'statistic'.date('YmdHis');
    }
}
