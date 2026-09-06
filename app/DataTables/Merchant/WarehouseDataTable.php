<?php

    namespace App\DataTables\Merchant;


    use App\Models\User;
    use App\Models\Warehouse;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Builder as QueryBuilder;
    use Yajra\DataTables\EloquentDataTable;
    use Yajra\DataTables\Html\Builder as HtmlBuilder;
    use Yajra\DataTables\Html\Column;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;
    use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
    use Yajra\DataTables\Services\DataTable;

    class WarehouseDataTable extends DataTable
    {
        public function dataTable(QueryBuilder $query): EloquentDataTable
        {
            return (new EloquentDataTable($query))
                ->addIndexColumn()
                ->addColumn('options', function ($warehouse) {
                    return view('merchant.warehouse.action', compact('warehouse'));
                })->addColumn('status', function ($warehouse) {
                    return view('merchant.warehouse.status', compact('warehouse'));
                })->addColumn('address', function ($warehouse) {
                    return view('merchant.warehouse.address', compact('warehouse'));
                })
                ->setRowId('id');
        }


        public function query(Warehouse $model, $pn = ''): QueryBuilder
        {
            $merchant_id = Sentinel::getUser()->merchant->id;
            $query = $model->where('merchant_id', $merchant_id)
                ->when($this->request->status ?? false, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when($this->request->search['value'] ?? false, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%")
                            ->orWhere('phone_number', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%");
                    });
                })
                ->latest();

            return $query->newQuery();
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
                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
                ->parameters([
                    'dom' => 'Blfrtip',
                    'buttons' => [
                        [],
                    ],
                    'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                    'language' => [
                        'searchPlaceholder' => __('search'),
                        'lengthMenu' => '_MENU_ ' . __('warehouse_per_page'),
                        'search' => '',
                    ],
                ]);
        }


        public function getColumns(): array
        {
            return [
                Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
                Column::computed('name')->title(__('name')),
                Column::computed('address')->title(__('address')),
                Column::computed('status')->title(__('status'))->exportable(false)
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
                Column::computed('options')->title(__('options'))->addClass('text-center'),

            ];
        }

        protected function filename(): string
        {
            return 'Warehouse_' . date('YmdHis');
        }
    }
