<?php

namespace App\DataTables;

use App\Models\NotificationUser;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Sentinel;

class NotificationDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('title', function ($row) {
                return $row->notification->title;
            })
            ->addColumn('description', function ($row) {
                return \Str::limit($row->notification->description, 50);
            })
            ->addColumn('status', function ($row) {
                if ($row->is_read == 1) {
                    return '<span class="badge badge-success px-2">' . __('read') . '</span>';
                } else {
                    return '<span class="badge badge-danger px-2">' . __('unread') . '</span>';
                }
            })
            ->addColumn('date', function ($row) {
                return date('d M Y h:i A', strtotime($row->created_at));
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . $row->notification->url . '" class="btn btn-sm btn-outline-primary text-nowrap"><i class="las la-external-link-alt"></i> ' . __('visit') . '</a>';
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    public function query(NotificationUser $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['notification', 'notification.createdBy'])
            ->where('user_id', Sentinel::getUser()->id)
            ->orderBy('created_at', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('notification-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function ( row, data, start, end, display ) {
                $(row).remove();
                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
            ->parameters([
                'dom' => 'Blfrtip',
                'buttons' => [],
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                'language' => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu' => '_MENU_ ' . __('per_page'),
                    'search' => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(50),
            Column::computed('title')->title(__('title')),
            Column::computed('description')->title(__('description')),
            Column::computed('date')->title(__('date')),
            Column::computed('status')->title(__('status')),
            Column::computed('action')->title(__('action'))->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Notification_' . date('YmdHis');
    }
}
