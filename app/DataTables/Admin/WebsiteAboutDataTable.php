<?php

namespace App\DataTables\Admin;

use App\Models\WebsiteAbout;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WebsiteAboutDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($about) {
                return view('admin.website.about.action', compact('about'));
            })->addColumn('status', function ($about) {
                return view('admin.website.about.status', compact('about'));
            })->addColumn('title', function ($about) {
                return @$about->language->title;
            })->addColumn('icon', function ($about) {
                return view('admin.website.about.image', compact('about'));
            })->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        $model = WebsiteAbout::with('language');

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
                    'lengthMenu'        => '_MENU_ '.__('about_per_page'),
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
            Column::make('title')->title(__('title')),
            Column::computed('status')->title(__('status'))->exportable(false)
                ->printable(false)
                ->searchable(false)->addClass('action-card text-end')->width(10),

        ];
    }

    protected function filename(): string
    {
        return 'iconabout'.date('YmdHis');
    }
}
