<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use App\Models\DeliveryMan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class DeliveryManDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
                ->addColumn('options', function ($delivery_man) {
            return view('admin.delivery-man.column.options', compact('delivery_man'));
        })->addColumn('name_email', function ($delivery_man) {
            return view('admin.delivery-man.column.name_email', compact('delivery_man'));
        })->addColumn('branch', function ($delivery_man) {
            return view('admin.delivery-man.column.branch', compact('delivery_man'));
        })->addColumn('address', function ($delivery_man) {
            return view('admin.delivery-man.column.address', compact('delivery_man'));
        })->addColumn('last_login', function ($delivery_man) {
            return view('admin.delivery-man.column.last_login', compact('delivery_man'));
        })->addColumn('fee', function ($delivery_man) {
            return view('admin.delivery-man.column.fee', compact('delivery_man'));
        })->addColumn('status', function ($delivery_man) {
            return view('admin.delivery-man.column.status', compact('delivery_man'));
        })->addColumn('current_amount', function ($delivery_man) {
            return view('admin.delivery-man.column.current_amount', compact('delivery_man'));
        })->setRowId('id');
    }
    public function query(DeliveryMan $model): QueryBuilder
    {
        $query = $model->when(!hasPermission('read_all_delivery_man'), function ($query){
                $query->whereHas('user', function ($q){
                    $q->where('branch_id', \Sentinel::getUser()->branch_id)
                        ->orWhere('branch_id', null);
                });
            })
            ->when($this->request->has('order'), function ($query) {
                $orderBy = $this->request->order[0]['dir'] ?? 'desc';
                $query->orderBy('created_at', $orderBy);
            })
            ->when($this->request->branch, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->when($this->request->branch == 'pending', function ($search){
                        $search->where('branch_id', null);
                    })->when($this->request->branch != 'pending', function ($search) {
                        $search->where('branch_id', $this->request->branch);
                    });
                });
            })
            ->when($this->request->has('status') && $this->request->status !== "", function ($query) {
                $status = $this->request->status;
                $query->where('status', $status);
            })
            ->when($this->request->has('email') && $this->request->email !== "", function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('email', 'like', '%' . $this->request->email . '%');
                });
            })
            ->when($this->request->has('name') && $this->request->name !== "",  function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('first_name', 'like', '%' . $this->request->name . '%')
                        ->orWhere('last_name', 'like', '%' . $this->request->name . '%');
                });
            })
            ->when(request('search')['value'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereHas('user', function ($q) use ($search) {
                              $q->where('first_name', 'like', "%$search%")
                                    ->orWhere('email', 'like', "%$search%")
                                    ->orWhere('phone_number', 'like', "%$search%");
                          });
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
                    'lengthMenu'        => '_MENU_ '.__('delivery_man_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('name_email')->title(__('name_email')),
            Column::computed('branch')->title(__('branch')),
            Column::computed('address')->title(__('address')),
            Column::computed('last_login')->title(__('login')),
            Column::computed('fee')->title(__('fee')),
            Column::computed('status')->title(__('status'))->exportable(false),
            Column::computed('current_amount')->title(__('amount'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'delivery_man'.date('YmdHis');
    }
}
