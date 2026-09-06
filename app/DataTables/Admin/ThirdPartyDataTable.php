<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\ThirdParty;
use Yajra\DataTables\Services\DataTable;

class ThirdPartyDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($third_party) {
            return view('admin.third-parties.column.options', compact('third_party'));
        })->addColumn('name', function ($third_party) {
            return view('admin.third-parties.column.name', compact('third_party'));
        })->addColumn('phone_number', function ($third_party) {
            return view('admin.third-parties.column.number', compact('third_party'));
        })->addColumn('address', function ($third_party) {
            return view('admin.third-parties.column.address', compact('third_party'));
        })->addColumn('status', function ($third_party) {
            return view('admin.third-parties.column.status', compact('third_party'));
        })->setRowId('id');
    }
    public function query(ThirdParty $model): QueryBuilder
    {
        $query = $model::query();

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%")
                ->orWhere('phone_number', 'like', "%$search%");
            });
        });
        return $query->latest();
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
                    'lengthMenu'        => '_MENU_ '.__('partner_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center')->width(60)->exportable(false)->printable(false),
            Column::computed('name')->title(__('name')),
            Column::computed('phone_number')->title(__('phone_number')),
            Column::computed('address')->title(__('address')),
            Column::computed('status')->title(__('status'))
            ->exportable(false)
            ->printable(false)
            ->width(60),

        ];
    }

    protected function filename(): string
    {
        return 'third_party'.date('YmdHis');
    }
}
