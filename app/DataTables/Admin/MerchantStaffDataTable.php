<?php

    namespace App\DataTables\Admin;

    use App\Models\Merchant;
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

    class MerchantStaffDataTable extends DataTable
    {
        public function dataTable(QueryBuilder $query): EloquentDataTable
        {
            return (new EloquentDataTable($query))
                ->addIndexColumn()
                ->addColumn('options', function ($query) {
                    return view('admin.merchants.details.staff.column.options', compact('query'));
                })->addColumn('user', function ($query) {
                    return view('admin.merchants.details.staff.column.user', compact('query'));
                })->addColumn('current_balance', function ($query) {
                    return view('admin.merchants.details.staff.column.current_balance', compact('query'));
                })->addColumn('phone_number', fn($query) => isDemoMode() ? '**************' : ($query->phone_number ?? ''))
                ->addColumn('last_login', function ($query) {
                    return $query->last_login ? date('M d, Y h:i a', strtotime($query->last_login)) : '';
                })
                ->addColumn('status', function ($query) {
                    return view('admin.merchants.details.staff.column.status', compact('query'));
                })
                ->setRowId('id');
        }


        public function query(User $model): Builder
        {

            $merchant = Merchant::where('id', $this->id)->first();

            return $merchant->staffs()->getQuery();
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
                        'lengthMenu' => '_MENU_ ' . __('staff_per_page'),
                        'search' => '',
                    ],
                ]);
        }


        public function getColumns(): array
        {
            return [
                Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
                Column::computed('options')->title(__('options'))->addClass('text-center'),
                Column::computed('user')->title(__('user')),
                Column::computed('current_balance')->title(__('current_balance')),
                Column::computed('phone_number')->title(__('phone_number')),
                Column::computed('last_login')->title(__('last_login')),
                Column::computed('status')->title(__('status'))->exportable(false)
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),

            ];
        }

        protected function filename(): string
        {
            return 'merchant_staff_' . date('YmdHis');
        }
    }
