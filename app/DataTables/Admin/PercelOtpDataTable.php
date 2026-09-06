<?php

namespace App\DataTables\Admin;

use App\Models\PercelOtpLog;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PercelOtpDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('created_at', function ($log) {
                return '<div class="fw-bold text-dark" style="white-space:nowrap;">' . date('d M Y', strtotime($log->created_at)) . '</div>'
                    . '<small class="text-muted" style="white-space:nowrap;">' . date('h:i:s A', strtotime($log->created_at)) . '</small>';
            })
            ->addColumn('parcel', function ($log) {
                if ($log->parcel) {
                    $url = route('admin.parcel.detail', $log->parcel->id);
                    $merchantName = @$log->parcel->shop->name ?? (@$log->parcel->merchant->company ?? (@$log->parcel->merchant->user->first_name . ' ' . @$log->parcel->merchant->user->last_name));
                    
                    $html = '<a href="' . $url . '" class="fw-bold text-primary font-14">' . e($log->parcel->parcel_no) . '</a>';
                    if ($log->parcel->customer_name || $log->parcel->customer_phone_number) {
                        $html .= '<div class="small text-dark mt-1"><i class="las la-user text-muted"></i> ' . e($log->parcel->customer_name ?: 'N/A') . '</div>';
                        $html .= '<div class="small text-muted"><i class="las la-phone text-muted"></i> ' . e($log->parcel->customer_phone_number) . '</div>';
                    }
                    if ($merchantName) {
                        $html .= '<div class="small text-secondary mt-1"><i class="las la-store text-info"></i> ' . e($merchantName) . '</div>';
                    }
                    return $html;
                }
                return '<span class="text-muted small">ID: #' . $log->parcel_id . '</span>';
            })
            ->addColumn('action_type', function ($log) {
                if ($log->action == 'success') {
                    return '<span class="badge bg-success font-12"><i class="las la-check-circle"></i> ' . __('otp_verified') . '</span>';
                } elseif ($log->action == 'wrong_attempt') {
                    return '<span class="badge bg-warning text-dark font-12"><i class="las la-exclamation-circle"></i> ' . __('wrong_attempt') . '</span>';
                } elseif ($log->action == 'locked') {
                    return '<span class="badge bg-danger font-12"><i class="las la-lock"></i> ' . __('otp_locked') . '</span>';
                } elseif ($log->action == 'expired') {
                    return '<span class="badge bg-secondary font-12"><i class="las la-clock"></i> ' . __('otp_expired') . '</span>';
                } elseif ($log->action == 'resend') {
                    return '<span class="badge bg-info text-white font-12"><i class="las la-redo"></i> ' . __('resend_otp') . '</span>';
                }
                return '<span class="badge bg-primary font-12"><i class="las la-paper-plane"></i> ' . __('otp_generated') . '</span>';
            })
            ->addColumn('otp_code', function ($log) {
                return $log->otp_code ? '<span class="badge bg-light text-dark border font-14 fw-bold px-2 py-1">' . e($log->otp_code) . '</span>' : '<span class="text-muted small">-</span>';
            })
            ->addColumn('submitted_otp', function ($log) {
                if ($log->submitted_otp) {
                    $colorClass = ($log->action == 'success') ? 'text-success' : 'text-danger';
                    return '<code class="fw-bold font-14 ' . $colorClass . '">' . e($log->submitted_otp) . '</code>';
                }
                return '<span class="text-muted small">-</span>';
            })
            ->addColumn('attempt_number', function ($log) {
                return $log->attempt_number > 0 ? '<span class="badge bg-light text-dark border font-12">' . $log->attempt_number . '/3</span>' : '<span class="text-muted small">-</span>';
            })
            ->addColumn('source', function ($log) {
                $src = strtolower($log->source);
                if ($src == 'rider_app') {
                    return '<span class="badge bg-primary"><i class="las la-mobile-alt"></i> Rider App</span>';
                } elseif ($src == 'rider_web') {
                    return '<span class="badge bg-info text-white"><i class="las la-globe"></i> Rider Web</span>';
                } elseif ($src == 'admin_panel') {
                    return '<span class="badge bg-dark"><i class="las la-user-shield"></i> Admin Panel</span>';
                }
                return '<span class="badge bg-secondary">' . strtoupper(str_replace('_', ' ', $log->source)) . '</span>';
            })
            ->addColumn('performed_by', function ($log) {
                if ($log->deliveryMan) {
                    $name = @$log->deliveryMan->user->first_name . ' ' . @$log->deliveryMan->user->last_name;
                    $phone = @$log->deliveryMan->phone_number ?? @$log->deliveryMan->user->phone_number;
                    $html = '<div class="small fw-bold text-dark"><i class="las la-motorcycle text-primary"></i> ' . e($name) . '</div>';
                    if ($phone) {
                        $html .= '<small class="text-muted"><i class="las la-phone"></i> ' . e($phone) . '</small>';
                    }
                    return $html;
                } elseif ($log->user) {
                    $name = @$log->user->first_name . ' ' . @$log->user->last_name;
                    $email = @$log->user->email;
                    $html = '<div class="small fw-bold text-dark"><i class="las la-user-shield text-info"></i> ' . e($name) . '</div>';
                    if ($email) {
                        $html .= '<small class="text-muted"><i class="las la-envelope"></i> ' . e($email) . '</small>';
                    }
                    return $html;
                }
                return '<span class="text-muted small">-</span>';
            })
            ->addColumn('status_message', function ($log) {
                return '<div class="small text-dark" style="min-width:140px;">' . e($log->status_message ?: '-') . '</div>';
            })
            ->addColumn('device_info', function ($log) {
                $html = '';
                if ($log->ip_address) {
                    $html .= '<div class="small text-dark"><i class="las la-network-wired text-primary"></i> <code>' . e($log->ip_address) . '</code></div>';
                }
                if ($log->user_agent) {
                    $html .= '<div class="small text-muted text-truncate mt-1" style="max-width:160px;" title="' . e($log->user_agent) . '"><i class="las la-desktop text-secondary"></i> ' . e(substr($log->user_agent, 0, 40)) . '...</div>';
                }
                return $html ?: '<span class="text-muted small">-</span>';
            })
            ->addColumn('options', function ($log) {
                if (hasPermission('percel_otp_delete')) {
                    return '<a href="javascript:void(0);" onclick="delete_row(\'percel-otp/delete/\', ' . $log->id . ')" class="btn btn-outline-danger btn-sm" title="' . __('delete') . '"><i class="las la-trash"></i></a>';
                }
                return '';
            })
            ->rawColumns(['created_at', 'parcel', 'action_type', 'otp_code', 'submitted_otp', 'attempt_number', 'source', 'performed_by', 'status_message', 'device_info', 'options'])
            ->setRowId(function ($log) {
                return 'row_' . $log->id;
            });
    }

    public function query(): QueryBuilder
    {
        $query = PercelOtpLog::with(['parcel.merchant.user', 'parcel.shop', 'user', 'deliveryMan.user'])->latest('id');

        $query->when(request('search')['value'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('otp_code', 'like', "%$search%")
                  ->orWhere('submitted_otp', 'like', "%$search%")
                  ->orWhere('action', 'like', "%$search%")
                  ->orWhere('source', 'like', "%$search%")
                  ->orWhere('status_message', 'like', "%$search%")
                  ->orWhere('ip_address', 'like', "%$search%")
                  ->orWhere('user_agent', 'like', "%$search%")
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
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:100%')
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
                    'lengthMenu'        => '_MENU_ ' . __('logs_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('created_at')->title(__('time')),
            Column::computed('parcel')->title(__('parcel_info')),
            Column::computed('action_type')->title(__('action')),
            Column::computed('otp_code')->title(__('otp_code')),
            Column::computed('submitted_otp')->title(__('input_code')),
            Column::computed('attempt_number')->title(__('attempt')),
            Column::computed('source')->title(__('source')),
            Column::computed('performed_by')->title(__('performed_by')),
            Column::computed('status_message')->title(__('status_message')),
            Column::computed('device_info')->title(__('ip_and_device')),
            Column::computed('options')->title(__('options'))->addClass('text-center')->exportable(false)->printable(false)->searchable(false)->width(30),
        ];
    }

    protected function filename(): string
    {
        return 'PercelOtpLogs_' . date('YmdHis');
    }
}
