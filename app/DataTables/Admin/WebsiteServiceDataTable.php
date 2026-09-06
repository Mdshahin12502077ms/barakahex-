<?php

namespace App\DataTables\Admin;

use App\Models\WebsiteService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WebsiteServiceDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($service) {
                return view('admin.website.service.action', compact('service'));
            })->addColumn('status', function ($service) {
                return view('admin.website.service.status', compact('service'));
            })->addColumn('title', function ($service) {
                return @$service->language->title;
            })->addColumn('image', function ($service) {
                return view('admin.website.service.image', compact('service'));
            })->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        $model = WebsiteService::with('language');

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
                    'lengthMenu'        => '_MENU_ '.__('service_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options')),
            Column::computed('image')->title(__('image')),
            Column::make('title')->title(__('title')),
            Column::computed('status')->title(__('status'))->exportable(false)
                ->printable(false)
                ->searchable(false)->addClass('action-card text-end')->width(10),

        ];
    }

    protected function filename(): string
    {
        return 'service'.date('YmdHis');
    }
}
