<?php

    namespace App\DataTables\Merchant;

    use App\Models\Merchant;
    use App\Models\Product;
    use App\Models\Stock;
    use App\Models\User;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Builder as QueryBuilder;
    use Yajra\DataTables\EloquentDataTable;
    use Yajra\DataTables\Html\Builder as HtmlBuilder;
    use Yajra\DataTables\Html\Column;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;
    use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
    use Yajra\DataTables\Services\DataTable;

    class StockDataTable extends DataTable
    {
        public function dataTable(QueryBuilder $query): EloquentDataTable
        {
            return (new EloquentDataTable($query))
                ->addIndexColumn()
                ->addColumn('name', function ($stock) {
                    return view('merchant.stock.product', compact('stock'));
                })->addColumn('warehouse', function ($stock) {
                    return view('merchant.stock.warehouse', compact('stock'));
                })->addColumn('status', function ($stock) {
                    return view('merchant.stock.status', compact('stock'));
                })
                ->setRowId('id');
        }

        public function query(Stock $model, $pn = ''): QueryBuilder
        {
            $query = $model->with(['product', 'warehouse'])
                ->whereHas('warehouse', function ($q) {
                    $q->where('merchant_id', Sentinel::getUser()->merchant->id);
                })
                ->when($this->request->search['value'] ?? false, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%")
                            ->orWhere('description', 'like', "%$search%");
                    });
                })
                ->latest();

            return $query;
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
                        'lengthMenu' => '_MENU_ ' . __('stock_per_page'),
                        'search' => '',
                    ],
                ]);
        }


        public function getColumns(): array
        {
            return [
                Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
                Column::computed('name')->title(__('product_name')),
                Column::computed('warehouse')->title(__('warehouse')),
                Column::computed('quantity')->title(__('quantity')),
                Column::computed('status')->title(__('status'))->addClass('text-end w-100'),
            ];
        }

        protected function filename(): string
        {
            return 'product_' . date('YmdHis');
        }
    }
