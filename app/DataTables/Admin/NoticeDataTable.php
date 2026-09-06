<?php
namespace App\DataTables\Admin;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use App\Models\Notice;
use Yajra\DataTables\Services\DataTable;

class NoticeDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($notice) {
            return view('admin.notice.column.options', compact('notice'));
        })->addColumn('title', function ($notice) {
            return view('admin.notice.column.title', compact('notice'));
        })
        ->addColumn('status', function ($notice) {
            return view('admin.notice.column.status', compact('notice'));
        })->addColumn('staff', function ($notice) {
            return view('admin.notice.column.staff', compact('notice'));
        })->addColumn('merchant', function ($notice) {
            return view('admin.notice.column.merchant', compact('notice'));
        })->setRowId('id');
    }
    public function query(Notice $model): QueryBuilder
    {
        $query = $model::query();

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                ->orWhere('details', 'like', "%$search%");
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
                    'lengthMenu'        => '_MENU_ '.__('notice_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('title')->title(__('title')),
            Column::computed('status')->title(__('status')),
            Column::computed('staff')->title(__('staff')),
            Column::computed('merchant')->title(__('merchant'))
            ->exportable(false)
            ->printable(false)
            ->width(60),

        ];
    }

    protected function filename(): string
    {
        return 'notice'.date('YmdHis');
    }
}
