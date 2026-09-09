<?php

namespace App\DataTables\Admin\DeliveryMan;

use App\Models\DeliveryMan;
use App\Models\Parcel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReturnTask extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('parcel_info', function ($parcel) {
                $url = route('deliveryman.parcel.detail', $parcel->id);
                $html = '<a href="' . $url . '" class="fw-bold text-primary font-14">' . e($parcel->parcel_no) . '</a>';
                $html .= '<div class="small text-muted mt-1"><i class="las la-calendar"></i> ' . date('d M Y, h:i A', strtotime($parcel->created_at)) . '</div>';
                return $html;
            })
            ->addColumn('merchant_info', function ($parcel) {
                $merchantName = @$parcel->shop->name ?? (@$parcel->merchant->company ?? (@$parcel->merchant->user->first_name . ' ' . @$parcel->merchant->user->last_name));
                $phone = @$parcel->pickup_shop_phone_number ?? @$parcel->merchant->phone_number;
                $address = @$parcel->pickup_address ?? @$parcel->merchant->address;
                
                $html = '<div class="fw-bold text-dark font-14"><i class="las la-store text-info"></i> ' . e($merchantName ?: 'N/A') . '</div>';
                if ($phone) {
                    $html .= '<div class="small text-muted"><i class="las la-phone"></i> ' . e($phone) . '</div>';
                }
                if ($address) {
                    $html .= '<div class="small text-secondary text-truncate" style="max-width: 200px;" title="' . e($address) . '"><i class="las la-map-marker-alt text-danger"></i> ' . e($address) . '</div>';
                }
                return $html;
            })
            ->addColumn('customer_info', function ($parcel) {
                $html = '<div class="small fw-bold text-dark">' . e($parcel->customer_name ?: 'N/A') . '</div>';
                if ($parcel->customer_phone_number) {
                    $html .= '<div class="small text-muted"><i class="las la-phone"></i> ' . e($parcel->customer_phone_number) . '</div>';
                }
                if ($parcel->customer_address) {
                    $html .= '<div class="small text-secondary text-truncate" style="max-width: 180px;" title="' . e($parcel->customer_address) . '">' . e($parcel->customer_address) . '</div>';
                }
                return $html;
            })
            ->addColumn('return_reason', function ($parcel) {
                $event = $parcel->events
                    ->where('title', '!=', 'parcel_return_to_merchant_event')
                    ->whereNotNull('cancel_note')
                    ->sortByDesc('id')
                    ->first();
                $note = $event ? $event->cancel_note : ($parcel->note ?? null);

                if (!$note) {
                    return '<span class="text-muted small">-</span>';
                }

                return '<div class="p-2 rounded bg-light border border-danger-subtle text-danger font-12" style="min-width:150px; max-width:240px; line-height:1.4; word-break:break-word; text-align:left;">
                            <i class="las la-exclamation-circle text-danger me-1"></i>' . e($note) . '
                        </div>';
            })
            ->addColumn('price_info', function ($parcel) {
                $html = '<div class="fw-bold text-dark">' . setting('default_currency') . ' ' . number_format($parcel->price, 2) . '</div>';
                $html .= '<small class="text-muted">' . $parcel->weight . ' KG</small>';
                if ($parcel->return_fee > 0) {
                    $html .= '<div class="small text-success mt-1">' . __('return_fee') . ': ' . setting('default_currency') . ' ' . number_format($parcel->return_fee, 2) . '</div>';
                }
                return $html;
            })
            ->addColumn('status', function ($parcel) {
                if ($parcel->status == 'returned-to-merchant') {
                    return '<span class="badge bg-success"><i class="las la-check-circle"></i> ' . __('returned_to_merchant') . '</span>';
                } elseif ($parcel->status == 'return-assigned-to-merchant') {
                    return '<span class="badge bg-warning text-dark"><i class="las la-shipping-fast"></i> ' . __('return_assigned_to_merchant') . '</span>';
                }
                return '<span class="badge bg-secondary">' . strtoupper(str_replace('-', ' ', $parcel->status)) . '</span>';
            })
            ->addColumn('action', function ($parcel) {
                $html = '<div class="d-inline-flex gap-1">';
                
                if ($parcel->status == 'return-assigned-to-merchant') {
                    $html .= '<form action="' . route('deliveryman.return.task.complete', $parcel->id) . '" method="POST" class="d-inline confirm-form"
                                data-title="' . __('are_you_sure') . '"
                                data-text="' . __('confirm_parcel_returned_to_merchant') . '"
                                data-confirm-btn="' . __('confirm_return') . '"
                                data-cancel-btn="' . __('cancel') . '">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-sm btn-success" title="' . __('mark_returned_to_merchant') . '">
                                    <i class="las la-check-double"></i> ' . __('return_to_merchant') . '
                                </button>
                            </form>';
                }
                
                $html .= '<a href="' . route('deliveryman.parcel.detail', $parcel->id) . '" class="btn btn-sm sg-btn-outline-primary" title="' . __('view_details') . '">
                            <i class="las la-eye"></i>
                          </a>';
                          
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['parcel_info', 'merchant_info', 'customer_info', 'return_reason', 'price_info', 'status', 'action'])
            ->setRowId(function ($parcel) {
                return 'row_' . $parcel->id;
            });
    }

    public function query(): QueryBuilder
    {
        $user = Sentinel::getUser();
        $deliveryMan = $user ? ($user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first()) : null;
        $deliveryManId = $deliveryMan ? $deliveryMan->id : 0;

        $query = Parcel::with(['merchant.user', 'shop', 'events'])
            ->where('return_delivery_man_id', $deliveryManId)
            ->whereIn('status', ['return-assigned-to-merchant', 'returned-to-merchant', 'return-cancel'])
            ->latest('id');

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('parcel_no', 'like', "%$search%")
                  ->orWhere('customer_name', 'like', "%$search%")
                  ->orWhere('customer_phone_number', 'like', "%$search%")
                  ->orWhere('pickup_shop_phone_number', 'like', "%$search%")
                  ->orWhereHas('merchant', function ($m) use ($search) {
                      $m->where('company', 'like', "%$search%");
                  });
            });
        });

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function ( row, data, start, end, display ) {
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
                    'lengthMenu'        => '_MENU_ ' . __('tasks_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('parcel_info')->title(__('parcel_info')),
            Column::computed('merchant_info')->title(__('merchant_info')),
            Column::computed('customer_info')->title(__('customer_info')),
            Column::computed('return_reason')->title(__('return_reason')),
            Column::computed('price_info')->title(__('price_and_fee')),
            Column::computed('status')->title(__('status')),
            Column::computed('action')->title(__('action'))->addClass('text-center')->exportable(false)->printable(false)->searchable(false)->width(60),
        ];
    }

    protected function filename(): string
    {
        return 'ReturnTasks_' . date('YmdHis');
    }
}
