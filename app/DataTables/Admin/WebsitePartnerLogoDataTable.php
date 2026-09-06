<?php

namespace App\DataTables\Admin;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\WebsitePartnerLogo;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use App\Models\Account\CompanyAccount;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class WebsitePartnerLogoDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($partner_logo) {
            return view('admin.website.partner_logo.column.action', compact('partner_logo'));
        })->addColumn('logo', function ($partner_logo) {
            return view('admin.website.partner_logo.column.logo', compact('partner_logo'));
        })->addColumn('status', function ($partner_logo) {
            return view('admin.website.partner_logo.column.status', compact('partner_logo'));
        })->setRowId('id');
    }

    public function query(): QueryBuilder
    {

        $model = WebsitePartnerLogo::latest();


        return $model->newQuery();
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
                    'lengthMenu'        => '_MENU_ '.__('partner_logo_per_page'),
                    'search'            => '',
                ],
            ]);
    }


    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options')),
            Column::computed('logo')->title(__('logo')),
            Column::computed('status')->title(__('status'))->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'statements_'.date('YmdHis');
    }
}
