@extends('backend.layouts.master')

@section('support', 'active')

@section('title')
    {{ __('ticket_details') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('ticket_details') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                    <div class="row g-gs">
                        <div class="col-md-12 col-sm-12 mb-4">
                            <div class="card-inner">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="title d-flex mb-2">
                                            <button type="button" class="btn btn-default text-info mx-0 px-0 border-0">#{{ $ticket->ticket_id }}</button>
                                        </h6>
                                        <h6 class="title">{{ $ticket->subject }}</h6>
                                    </div>
                                    @if(hasPermission('support_ticket_update'))
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold">{{ __('status') }}:</span>
                                        <select id="inline-status-select" class="form-select form-select-sm" data-id="{{ $ticket->id }}" style="min-width: 150px;">
                                            @foreach(['new','open','processing','resolved','closed'] as $s)
                                                <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-xl-6">
                            <div class="card parcel__details wave flex-column h-100 d-flex mb-4">
                                <div class="card-header">{{ __('ticket_information') }}</div>
                                <div class="card-body">
                                    <span>{{ __('type') }}:&nbsp;</span> <span class="text">{{ ucwords(str_replace('_',' ',$ticket->ticket_type)) }}</span><br>
                                    <span>{{ __('priority') }}:&nbsp;</span> <span class="text">{{ ucfirst($ticket->priority) }}</span><br>
                                    <span>{{ __('status') }}:&nbsp;</span> <span class="text">{{ ucfirst($ticket->status) }}</span><br>
                                    <span>{{ __('created_at') }}:&nbsp;</span> <span class="text">{{ $ticket->created_at->format('M d, Y h:i A') }}</span><br>
                                    @if($ticket->assignedStaff)
                                        <span>{{ __('assigned_to') }}:&nbsp;</span> <span class="text">{{ $ticket->assignedStaff->first_name ?? '' }} {{ $ticket->assignedStaff->last_name ?? '' }}</span><br>
                                    @endif
                                    
                                    <hr>
                                    <span>{{ __('description') }}:</span> <br>
                                    <span class="text d-block mt-2">{{ $ticket->description }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card parcel__details wave flex-column h-100 d-flex mb-4">
                                <div class="card-header">{{ __('merchant_details') }}</div>
                                <div class="card-body">
                                    @if($ticket->merchant)
                                        <span>{{ __('company_name') }}:&nbsp;</span> <span class="text">{{ $ticket->merchant->company ?? '—' }}</span><br>
                                        <span>{{ __('email') }}:&nbsp;</span> <span class="text">{{ $ticket->merchant->email ?? '—' }}</span><br>
                                        <span>{{ __('phone') }}:&nbsp;</span> <span class="text">{{ $ticket->merchant->mobile ?? '—' }}</span><br>
                                    @else
                                        <span class="text">{{ __('N/A') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($ticket->parcel)
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card parcel__details wave flex-column h-100 d-flex mb-4">
                                <div class="card-header">{{ __('parcel_information') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span>{{ __('parcel_no') }}:&nbsp;</span> <span class="text">{{ $ticket->parcel->parcel_no ?? '—' }}</span><br>
                                            <span>{{ __('parcel_status') }}:&nbsp;</span> <span class="text">{{ ucwords(str_replace('-',' ', $ticket->parcel->status ?? '—')) }}</span><br>
                                        </div>
                                        <div class="col-md-6">
                                            <span>{{ __('recipient') }}:&nbsp;</span> <span class="text">{{ $ticket->parcel->recipient_name ?? '—' }}</span><br>
                                            <span>{{ __('recipient_phone') }}:&nbsp;</span> <span class="text">{{ $ticket->parcel->recipient_phone ?? '—' }}</span><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($ticket->attachments && $ticket->attachments->count())
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card parcel__details wave flex-column h-100 d-flex mb-4">
                                <div class="card-header">{{ __('attachments') }}</div>
                                <div class="card-body d-flex flex-wrap gap-3">
                                    @foreach($ticket->attachments as $att)
                                        @php
                                            $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        
                                        @if($isImage)
                                            <a href="{{ asset($att->file_path) }}" target="_blank" class="d-inline-block border p-1 rounded">
                                                <img src="{{ asset($att->file_path) }}" alt="Attachment" style="max-height: 120px; object-fit: contain;">
                                            </a>
                                        @else
                                            <a href="{{ asset($att->file_path) }}" target="_blank" class="btn border d-flex align-items-center">
                                                <i class="las la-paperclip me-1"></i> {{ basename($att->file_path) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $('#inline-status-select').on('change', function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        var status = $(this).val();
        var ticketId = $(this).data('id');
        
        var $select = $(this);
        $select.prop('disabled', true);

        $.ajax({
            url : '{{ route("admin.support.status") }}',
            type: 'POST',
            data: { status: status, ticketId: ticketId },
            success: function (response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'Status updated!');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    toastr.error(response.message || 'Something went wrong!');
                    $select.prop('disabled', false);
                }
            },
            error: function () {
                toastr.error('Failed to update status.');
                $select.prop('disabled', false);
            }
        });
    });
</script>
@endpush
