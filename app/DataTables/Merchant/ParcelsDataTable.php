<?php

namespace App\DataTables\Merchant;

use App\Models\Parcel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Yajra\DataTables\Services\DataTable;

class ParcelsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($parcel) {
                return view('merchant.parcel.column.action', compact('parcel'));
            })->addColumn('no', function ($parcel) {
                return view('merchant.parcel.column.no', compact('parcel'));
            })->addColumn('merchant', function ($parcel) {
                return view('merchant.parcel.column.merchant', compact('parcel'));
            })->addColumn('customer', function ($parcel) {
                return view('merchant.parcel.column.customer', compact('parcel'));
            })->addColumn('status', function ($parcel) {
                return view('merchant.parcel.column.status', compact('parcel'));
            })->rawColumns(['options', 'no', 'merchant', 'customer', 'status'])
            ->setRowId('id');
    }

    public function query(Parcel $model, $pn = ''): QueryBuilder
    {
        $query = $model->query()->with([
            'merchant.user',
            'deliveryMan.user',
            'returnDeliveryMan.user',
            'pickupMan.user',
            'shop',
            'destinationBranch'
        ]);

        if (Sentinel::getUser()->user_type == 'merchant_staff') {
            $query->where('merchant_id', Sentinel::getUser()->merchant_id)
                ->when(!hasPermission('all_parcel'), function ($query) {
                    return $query->whereHas('shop', function ($q) {
                        $q->whereIn('id', \Sentinel::getUser()->shops);
                    });
                });
        }

        if (Sentinel::getUser()->user_type == 'merchant') {
            $query->where('merchant_id', Sentinel::getUser()->merchant->id)
                ->where('parcel_no', 'like', '%' . $pn . '%');
        }

        $query->when($this->request->customer_name ?? false, function ($query, $customer_name) {
            $query->where('customer_name', 'like', "%$customer_name%");
        })
            ->when($this->request->customer_invoice_no ?? false, function ($query, $customer_invoice_no) {
                $query->where('customer_invoice_no', $customer_invoice_no);
            })
            ->when($this->request->phone_number ?? false, function ($query, $phone_number) {
                $query->where('customer_phone_number', $phone_number);
            })
            ->when($this->request->created_date ?? false, function ($query, $created_date) {
                $dateRange = $this->parseDate($created_date);
                $query->whereBetween('created_at', $dateRange);
            })
            ->when($this->request->pickup_date ?? false, function ($query, $pickup_date) {
                $formattedDate = \Carbon\Carbon::parse($pickup_date)->format('Y-m-d');
                $query->whereDate('pickup_date', $formattedDate);
            })
            ->when($this->request->delivery_date ?? false, function ($query, $delivery_date) {
                $formattedDate = \Carbon\Carbon::parse($delivery_date)->format('Y-m-d');
                $query->whereDate('delivery_date', $formattedDate);
            })
            ->when($this->request->delivered_date ?? false, function ($query, $delivered_date) {
                $query->where('delivered_date', $delivered_date);
            })
            ->when($this->request->status ?? false, function ($query, $status) {
                if ($status == 'pending-return') {
                    $query->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
                } elseif ($status == 'partially-delivered') {
                    $query->whereIn('status', ['partially-delivered', 'returned-to-merchant'])->where('is_partially_delivered', 1);
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($this->request->weight ?? false, function ($query, $weight) {
                $query->where('weight', $weight);
            })
            ->when($this->request->parcel_type ?? false, function ($query, $parcel_type) {
                $query->where('parcel_type', $parcel_type);
            })
            ->when($this->request->location ?? false, function ($query, $location) {
                $query->where('location', $location);
            })
            ->latest();

        return $query->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function (row, data, start, end, display ) {
                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
            ->parameters([
                'dom'        => "<'row mb-3 align-items-center'<'col-sm-6'l><'col-sm-6 text-end'f>><'table-responsive't><'row mt-3 align-items-center'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ ' . __('parcel_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('no')->title(__('parcel_info')),
            Column::computed('merchant')->title(__('price_and_fee')),
            Column::computed('customer')->title(__('customer')),
            Column::computed('status')->title(__('status'))
                ->exportable(false)
                ->printable(false)
                ->width(140)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'parcel_' . date('YmdHis');
    }

    private function parseDate($date_range)
    {
        $dates = explode('to', $date_range);

        if (count($dates) == 1) {
            $dates[1] = $dates[0];
        }

        $start_date = trim($dates[0]) . ' 00:00:00';
        $end_date   = trim($dates[1]) . ' 23:59:59';

        return [
            Carbon::parse($start_date)->format('Y-m-d H:i:s'),
            Carbon::parse($end_date)->format('Y-m-d H:i:s'),
        ];
    }
}
