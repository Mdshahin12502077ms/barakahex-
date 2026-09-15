<?php

namespace App\DataTables\Admin;

use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Yajra\DataTables\Services\DataTable;

class AdminSupportDataTables extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('ticket_id', function ($ticket) {
                $url = \Illuminate\Support\Facades\Route::has('merchant.support-tickets.show')
                    ? route('merchant.support-tickets.show', $ticket->id)
                    : '#';
                return '<a href="' . $url . '" class="text-primary fw-bold"><i class="las la-ticket-alt"></i> ' . e($ticket->ticket_id) . '</a>';
            })
            ->addColumn('ticket_type', function ($ticket) {
                return '<span class="badge bg-light text-dark border">' . ucwords(str_replace('_', ' ', $ticket->ticket_type)) . '</span>';
            })
            ->addColumn('tracking_number', function ($ticket) {
                $tracking = $ticket->tracking_number ?? ($ticket->parcel ? $ticket->parcel->parcel_no : null);
                if ($tracking) {
                    return '<span class="badge bg-secondary text-white"><i class="las la-barcode"></i> ' . e($tracking) . '</span>';
                }
                return '<span class="text-muted">---</span>';
            })
            ->addColumn('subject', function ($ticket) {
                $subject = '<strong>' . e($ticket->subject) . '</strong>';
                $desc = '<br><small class="text-muted">' . Str::limit(e($ticket->description), 50) . '</small>';
                return $subject . $desc;
            })
            ->addColumn('priority', function ($ticket) {
                $badges = [
                    'low'    => 'bg-info text-white',
                    'medium' => 'bg-primary text-white',
                    'high'   => 'bg-warning text-dark',
                    'urgent' => 'bg-danger text-white',
                ];
                $class = $badges[$ticket->priority] ?? 'bg-secondary text-white';
                return '<span class="badge ' . $class . '">' . ucfirst($ticket->priority) . '</span>';
            })
           
              ->addColumn('status', function ($ticket) {
                return view('admin.supports.status', compact('ticket'));
              })


            ->addColumn('created_at', function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M d, Y h:i A');
            })
            ->addColumn('options', function ($ticket) {
                $viewUrl = \Illuminate\Support\Facades\Route::has('admin.supports.show')
                    ? route('admin.supports.show', $ticket->id)
                    : '#';
                $phone = $ticket->merchant ? $ticket->merchant->mobile : ($ticket->user ? $ticket->user->mobile : '');
                // Clean the phone number (remove spaces, +, etc. for wa.me)
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                
                $messageText = urlencode("Hello, we are contacting you regarding your Support Ticket #{$ticket->ticket_id}: {$ticket->subject}");
                
                $whatsappUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text={$messageText}" : '#';

                $deleteUrl = \Illuminate\Support\Facades\Route::has('admin.supports.delete')
                    ? route('admin.supports.delete', $ticket->id)
                    : '#';

                return '
                    <div class="d-flex gap-1">
                        <a href="' . $viewUrl . '" class="btn btn-sm btn-outline-primary" title="View">
                            <i class="las la-eye"></i> ' . __('View') . '
                        </a>
                        <a href="' . $whatsappUrl . '" target="_blank" class="btn btn-sm btn-outline-success ' . (!$cleanPhone ? 'disabled' : '') . '" title="WhatsApp">
                            <i class="lab la-whatsapp"></i> ' . __('WhatsApp') . '
                        </a>
                        <a href="' . $deleteUrl . '" class="btn btn-sm btn-outline-danger" title="Delete">
                            <i class="las la-trash"></i> ' . __('Delete') . '
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['ticket_id', 'ticket_type', 'tracking_number', 'subject', 'priority', 'status', 'options'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SupportTicket $model): QueryBuilder
    {
        // Admin sees ALL tickets (no merchant filter)
        $query = $model->newQuery()
            ->with(['parcel']);

        // Status Filter
        if ($this->request->get('status')) {
            $query->where('status', $this->request->get('status'));
        }

        // Priority Filter
        if ($this->request->get('priority')) {
            $query->where('priority', $this->request->get('priority'));
        }

        // Ticket Type Filter
        if ($this->request->get('ticket_type')) {
            $query->where('ticket_type', $this->request->get('ticket_type'));
        }

        // Global Search
        $search = $this->request->input('search.value');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:100%')
            ->parameters([
                'dom' => 'Blfrtip',
                'buttons' => [],
                'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
                'language' => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu' => '_MENU_ ' . __('tickets_per_page'),
                    'search' => '',
                ],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(10)->searchable(false)->orderable(false),
            Column::make('ticket_id')->title(__('Ticket ID')),
            Column::make('ticket_type')->title(__('Type')),
            Column::computed('tracking_number')->title(__('Tracking / Parcel')),
            Column::make('subject')->title(__('Subject')),
            Column::make('priority')->title(__('Priority')),
            Column::make('status')->title(__('Status')),
            Column::make('created_at')->title(__('Created At')),
            Column::computed('options')->title(__('Actions'))->addClass('text-center')->searchable(false)->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SupportTickets_' . date('YmdHis');
    }
}
