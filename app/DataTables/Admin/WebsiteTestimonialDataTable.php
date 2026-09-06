<?php

namespace App\DataTables\Admin;

use App\Models\WebsiteTestimonial;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WebsiteTestimonialDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($testimonial) {
                return view('admin.website.testimonial.action', compact('testimonial'));
            })->addColumn('status', function ($testimonial) {
                return view('admin.website.testimonial.status', compact('testimonial'));
            })->addColumn('rating', function ($testimonial) {
                return view('admin.website.testimonial.rating', compact('testimonial'));
            })->addColumn('image', function ($testimonial) {
                return view('admin.website.testimonial.image', compact('testimonial'));
            })->addColumn('designation', function ($testimonial) {
                return @$testimonial->language->designation;
            })->addColumn('name', function ($testimonial) {
                return @$testimonial->language->name;
            })->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        $model = WebsiteTestimonial::with('language');

        return $model->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->whereHas('language', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('designation', 'like', "%$search%");
            });
        })->latest()->newQuery();
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
                    'lengthMenu'        => '_MENU_ '.__('testimonial_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options')),
            Column::make('name')->title(__('name')),
            Column::make('designation')->title(__('designation')),
            Column::computed('image')->title(__('image')),
            Column::make('rating')->title(__('rating')),
            Column::computed('status')->title(__('status'))->exportable(false)
                ->printable(false)
                ->searchable(false)->addClass('action-card text-end')->width(10),

        ];
    }

    protected function filename(): string
    {
        return 'testimonial_'.date('YmdHis');
    }
}
