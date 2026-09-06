<?php

namespace App\DataTables\Admin;

use App\Models\Merchant;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Yajra\DataTables\Services\DataTable;

class MerchantShopDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($shop) {
            return view('admin.merchants.details.shop.column.options', compact('shop'));
        })->addColumn('shop', function ($shop) {
            return view('admin.merchants.details.shop.column.shop_name', compact('shop'));
        })->addColumn('contact', function ($shop) {
            return view('admin.merchants.details.shop.column.contact_number', compact('shop'));
        })->addColumn('pickup', function ($shop) {
            return view('admin.merchants.details.shop.column.pickup_number', compact('shop'));
        })->addColumn('address', function ($shop) {
            return view('admin.merchants.details.shop.column.address', compact('shop'));
        })->addColumn('default', function ($shop) {
            return view('admin.merchants.details.shop.column.default', compact('shop'));
        })
        ->setRowId('id');
    }


    public function query(Shop $model): Builder
    {
        $merchant = Merchant::find($this->id);
        $branch_match = $merchant?->withPermission($merchant->id)->get();

        if (hasPermission('read_all_merchant') || $branch_match || $branch_match == '') {
            $query = $model->where('merchant_id', $merchant->id)
                ->when($this->request->status ?? false, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when($this->request->search['value'] ?? false, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('shop_name', 'like', "%$search%")
                            ->orWhere('shop_phone_number', 'like', "%$search%")
                            ->orWhere('contact_number', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%");
                    });
                })
                ->latest();

            return $query->newQuery();
        }

        abort(403, __('access_denied'));
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
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ '.__('shop_per_page'),
                    'search'            => '',
                ],
            ]);
    }


    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false),
            Column::computed('options')->addClass('text-center')->title(__('options')),
            Column::computed('shop')->title(__('shop'))->width(10),
            Column::computed('contact')->title(__('contact')),
            Column::computed('pickup')->title(__('pickup')),
            Column::computed('address')->title(__('address')),
            Column::computed('default')->addClass('text-center')->title(__('default'))->exportable(false)
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'merchant_shop_'.date('YmdHis');
    }
}
