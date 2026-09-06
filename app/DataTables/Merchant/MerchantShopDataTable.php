<?php

namespace App\DataTables\Merchant;

use App\Models\Merchant;
use App\Models\Shop;
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

class MerchantShopDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addIndexColumn()
        ->addColumn('options', function ($shop) {
            return view('merchant.profile.shop.column.action', compact('shop'));
        })->addColumn('shop_name', function ($shop) {
            return view('merchant.profile.shop.column.shop_name', compact('shop'));
        })->addColumn('contact_number', function ($shop) {
            return view('merchant.profile.shop.column.contact_number', compact('shop'));
        })->addColumn('pickup_number', function ($shop) {
            return view('merchant.profile.shop.column.pickup_number', compact('shop'));
        })->addColumn('address', function ($shop) {
            return view('merchant.profile.shop.column.pickup_address', compact('shop'));
        })->addColumn('default', function ($shop) {
            return view('merchant.profile.shop.column.default', compact('shop'));
        })
        ->setRowId('id');
    }

    public function query(Shop $model): QueryBuilder
    {
        $user = Sentinel::getUser();

        $merchant_id = $user->user_type === 'merchant'
            ? $user->merchant->id
            : $user->staffMerchant->merchant_id;

        $query = $model->where('merchant_id', $merchant_id)
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
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options')),
            Column::computed('shop_name')->title(__('shop_name')),
            Column::computed('contact_number')->title(__('contact_number')),
            Column::computed('pickup_number')->title(__('pickup_number')),
            Column::computed('address')->title(__('address')),
            Column::computed('default')->title(__('default'))->exportable(false)
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }


    protected function filename(): string
    {
        return 'merchant_staff_'.date('YmdHis');
    }
}
