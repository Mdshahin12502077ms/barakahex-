<?php

namespace App\DataTables\Admin;

use App\Models\ParcelEvent;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AuditLogDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('date_time', function ($event) {
                return '<div class="fw-bold text-dark" style="white-space:nowrap;">' . date('d M Y', strtotime($event->created_at)) . '</div>'
                    . '<small class="text-muted" style="white-space:nowrap;"><i class="las la-clock"></i> ' . date('h:i:s A', strtotime($event->created_at)) . '</small>';
            })
            ->addColumn('performed_by', function ($event) {
                if ($event->user) {
                    $name = trim(@$event->user->first_name . ' ' . @$event->user->last_name);
                    $email = @$event->user->email;
                    $roleName = @$event->user->roles->first()->name ?? 'Staff';

                    $html = '<div class="fw-bold text-dark"><i class="las la-user-circle text-primary"></i> ' . e($name ?: $email) . '</div>';
                    $html .= '<div class="small text-muted"><span class="badge bg-light text-secondary border font-11">' . e($roleName) . '</span></div>';
                    if ($email && $name) {
                        $html .= '<small class="text-muted font-11">' . e($email) . '</small>';
                    }
                    return $html;
                }
                return '<span class="badge bg-secondary font-12"><i class="las la-robot"></i> System</span>';
            })
            ->addColumn('action_badge', function ($event) {
                $action = strtolower($event->action ?? '');
                switch ($action) {
                    case 'cod_change':
                        return '<span class="badge bg-warning text-dark font-12"><i class="las la-money-bill-wave"></i> COD Changed</span>';
                    case 'charge_change':
                        return '<span class="badge bg-info text-white font-12"><i class="las la-calculator"></i> Charge Changed</span>';
                    case 'rider_assign':
                        return '<span class="badge text-white font-12" style="background-color:#6f42c1;"><i class="las la-motorcycle"></i> Rider Assigned</span>';
                    case 'return':
                        return '<span class="badge bg-danger font-12"><i class="las la-undo"></i> Return</span>';
                    case 'settlement':
                        return '<span class="badge bg-success font-12"><i class="las la-wallet"></i> Settlement</span>';
                    case 'status_change':
                    default:
                        return '<span class="badge bg-primary font-12"><i class="las la-sync"></i> Status Change</span>';
                }
            })
            ->addColumn('parcel_info', function ($event) {
                if ($event->parcel) {
                    $url = route('admin.parcel.detail', $event->parcel->id);
                    $merchantName = @$event->parcel->shop->name ?? (@$event->parcel->merchant->company ?? trim(@$event->parcel->merchant->user->first_name . ' ' . @$event->parcel->merchant->user->last_name));

                    $html = '<a href="' . $url . '" class="fw-bold text-primary font-14"><i class="las la-box"></i> #' . e($event->parcel->parcel_no) . '</a>';
                    if ($event->parcel->customer_name || $event->parcel->customer_phone_number) {
                        $html .= '<div class="small text-dark mt-1"><i class="las la-user text-muted"></i> ' . e($event->parcel->customer_name ?: 'N/A') . '</div>';
                        $html .= '<div class="small text-muted"><i class="las la-phone text-muted"></i> ' . e($event->parcel->customer_phone_number) . '</div>';
                    }
                    if ($merchantName) {
                        $html .= '<div class="small text-secondary mt-1"><i class="las la-store text-info"></i> ' . e($merchantName) . '</div>';
                    }
                    return $html;
                }

                // If no parcel (e.g. settlement event)
                $extra = json_decode($event->additional_info, true);
                if (is_array($extra) && isset($extra['merchant_name'])) {
                    return '<div class="fw-bold text-dark"><i class="las la-store text-info"></i> ' . e($extra['merchant_name']) . '</div>'
                        . '<small class="text-muted">Withdraw #' . e($extra['withdraw_id'] ?? '-') . '</small>';
                }

                return '<span class="text-muted small">-</span>';
            })
            ->addColumn('old_status', function ($event) {
                if (!empty($event->old_status)) {
                    return '<span class="badge bg-light text-dark border font-12">' . e(ucwords(str_replace(['-', '_'], ' ', $event->old_status))) . '</span>';
                }
                return '<span class="text-muted small">-</span>';
            })
            ->addColumn('new_status', function ($event) {
                if (!empty($event->new_status)) {
                    return '<span class="badge bg-secondary text-white font-12">' . e(ucwords(str_replace(['-', '_'], ' ', $event->new_status))) . '</span>';
                }
                return '<span class="text-muted small">-</span>';
            })
            ->addColumn('change_details', function ($event) {
                $extra = json_decode($event->additional_info, true);
                if (is_array($extra)) {
                    // COD Change
                    if (isset($extra['type']) && $extra['type'] == 'cod') {
                        return '<div class="font-12">'
                            . '<span class="text-muted">Old:</span> <strong class="text-danger">' . (setting('currency_symbol') ?? '৳') . ' ' . number_format((float)@$extra['old_value'], 2) . '</strong><br>'
                            . '<span class="text-muted">New:</span> <strong class="text-success">' . (setting('currency_symbol') ?? '৳') . ' ' . number_format((float)@$extra['new_value'], 2) . '</strong>'
                            . '</div>';
                    }
                    // Charge Change
                    if (isset($extra['type']) && $extra['type'] == 'charge') {
                        return '<div class="font-12">'
                            . '<span class="text-muted">Old:</span> <strong class="text-danger">' . (setting('currency_symbol') ?? '৳') . ' ' . number_format((float)@$extra['old_charge'], 2) . '</strong><br>'
                            . '<span class="text-muted">New:</span> <strong class="text-success">' . (setting('currency_symbol') ?? '৳') . ' ' . number_format((float)@$extra['new_charge'], 2) . '</strong>'
                            . '</div>';
                    }
                    // Settlement
                    if (isset($extra['type']) && $extra['type'] == 'settlement') {
                        return '<div class="font-12">'
                            . '<span>Amount: <strong class="text-primary">' . (setting('currency_symbol') ?? '৳') . ' ' . number_format((float)@$extra['amount'], 2) . '</strong></span><br>'
                            . '<small class="text-muted">' . e(@$extra['description']) . '</small>'
                            . '</div>';
                    }
                    // Rider Assign
                    if (isset($extra['type']) && $extra['type'] == 'rider_assign') {
                        return '<div class="font-12 text-dark"><i class="las la-motorcycle text-primary"></i> ' . e(@$extra['description'] ?? 'Rider Assigned') . '</div>';
                    }
                    // General description
                    if (isset($extra['description'])) {
                        return '<div class="small text-dark">' . e($extra['description']) . '</div>';
                    }
                }

                if (!empty($event->additional_info) && !is_array($extra)) {
                    return '<div class="small text-muted">' . e($event->additional_info) . '</div>';
                }

                if ($event->cancel_note) {
                    return '<div class="small text-muted"><i class="las la-comment"></i> ' . e($event->cancel_note) . '</div>';
                }

                return '<span class="text-muted small">-</span>';
            })
            ->addColumn('ip_address', function ($event) {
                if ($event->ip_address) {
                    return '<div class="small text-dark"><i class="las la-network-wired text-secondary"></i> <code>' . e($event->ip_address) . '</code></div>';
                }
                return '<span class="text-muted small">-</span>';
            })
            ->rawColumns(['date_time', 'performed_by', 'action_badge', 'parcel_info', 'old_status', 'new_status', 'change_details', 'ip_address'])
            ->setRowId(function ($event) {
                return 'row_' . $event->id;
            });
    }

    public function query(): QueryBuilder
    {
        // Fetch events that have audit action or are status/event transitions
        $query = ParcelEvent::with(['parcel.merchant.user', 'parcel.shop', 'user.roles', 'deliveryPerson.user', 'branch'])
            ->latest('id');

        // Filter by Action Type
        if (request()->has('action_type') && !empty(request('action_type'))) {
            $query->where('action', request('action_type'));
        }

        // Filter by Date Range
        if (request()->filled('from_date')) {
            $query->whereDate('created_at', '>=', request('from_date'));
        }
        if (request()->filled('to_date')) {
            $query->whereDate('created_at', '<=', request('to_date'));
        }

        // Global Search
        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                  ->orWhere('title', 'like', "%$search%")
                  ->orWhere('old_status', 'like', "%$search%")
                  ->orWhere('new_status', 'like', "%$search%")
                  ->orWhere('ip_address', 'like', "%$search%")
                  ->orWhere('additional_info', 'like', "%$search%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  })
                  ->orWhereHas('parcel', function ($p) use ($search) {
                      $p->where('parcel_no', 'like', "%$search%")
                        ->orWhere('customer_name', 'like', "%$search%")
                        ->orWhere('customer_phone_number', 'like', "%$search%");
                  });
            });
        });

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('audit-logs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:100%')
            ->initComplete('function () {
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
                    'lengthMenu'        => '_MENU_ ' . __('logs_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('date_time')->title(__('date') . ' & ' . __('time')),
            Column::computed('performed_by')->title(__('User')),
            Column::computed('action_badge')->title(__('Action')),
            Column::computed('parcel_info')->title(__('Parcel')),
            Column::computed('old_status')->title(__('Old Status')),
            Column::computed('new_status')->title(__('New Status')),
            Column::computed('change_details')->title(__('Details / Changes')),
            Column::computed('ip_address')->title(__('IP')),
        ];
    }

    protected function filename(): string
    {
        return 'AuditLogs_' . date('YmdHis');
    }
}
