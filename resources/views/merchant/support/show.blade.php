@extends('backend.layouts.master')

@section('title')
    {{ __('Support Ticket') }} - {{ $ticket->ticket_id }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <!-- Header -->
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <div>
                        <h3 class="section-title mb-1">{{ __('Ticket') }}: {{ $ticket->ticket_id }}</h3>
                        <p class="text-muted mb-0">{{ $ticket->subject }}</p>
                    </div>
                    <div class="oftions-content-right">
                        <a href="{{ route('merchant.support-tickets.index') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('Back to Tickets') }}</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Conversation / Thread -->
                    <div class="col-lg-8">
                        <div class="card bg-white redious-border p-20 p-sm-30 mb-4">
                            <!-- Original Ticket Query -->
                            <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-weight: bold;">
                                    <i class="las la-user"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h5 class="mb-0 fw-bold">{{ @$ticket->user->first_name . ' ' . @$ticket->user->last_name }} <small class="badge bg-light text-dark border ms-1">{{ __('You') }}</small></h5>
                                        <small class="text-muted"><i class="las la-clock"></i> {{ $ticket->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <div class="ticket-description mt-2 text-dark" style="white-space: pre-line; line-height: 1.6;">
                                        {{ $ticket->description }}
                                    </div>

                                    @if($ticket->attachments && $ticket->attachments->whereNull('reply_id')->count() > 0)
                                        <div class="mt-3">
                                            <label class="fw-bold text-muted mb-1"><small><i class="las la-paperclip"></i> {{ __('Attachments') }}:</small></label>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($ticket->attachments->whereNull('reply_id') as $att)
                                                    <a href="{{ asset($att->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                                        <i class="las la-file-download"></i> {{ $att->file_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Replies List -->
                            <div class="replies-thread mb-4">
                                @forelse($ticket->replies->where('is_internal_note', false) as $reply)
                                    @php
                                        $isStaff = $reply->user_id != Sentinel::getUser()->id;
                                    @endphp
                                    <div class="d-flex gap-3 mb-4 pb-4 border-bottom {{ $isStaff ? 'bg-light p-3 rounded' : '' }}">
                                        <div class="avatar {{ $isStaff ? 'bg-success text-white' : 'bg-primary text-white' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-weight: bold;">
                                            <i class="las {{ $isStaff ? 'la-headset' : 'la-user' }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 fw-bold">
                                                    {{ @$reply->user->first_name . ' ' . @$reply->user->last_name }}
                                                    @if($isStaff)
                                                        <span class="badge bg-success text-white ms-1">{{ __('Support Staff') }}</span>
                                                    @else
                                                        <span class="badge bg-light text-dark border ms-1">{{ __('You') }}</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted"><i class="las la-clock"></i> {{ $reply->created_at->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <div class="reply-text mt-2 text-dark" style="white-space: pre-line; line-height: 1.6;">
                                                {{ $reply->reply_text }}
                                            </div>

                                            @if($reply->attachments && $reply->attachments->count() > 0)
                                                <div class="mt-2">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($reply->attachments as $att)
                                                            <a href="{{ asset($att->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                                                <i class="las la-file-download"></i> {{ $att->file_name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3 text-muted">
                                        <i class="las la-comments fa-2x mb-2 d-block"></i>
                                        <p class="mb-0">{{ __('No replies yet. Support team will respond shortly.') }}</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Reply Form (Only if ticket is not closed) -->
                            @if($ticket->status != 'closed')
                                <div class="reply-form mt-3">
                                    <h5 class="fw-bold mb-3"><i class="las la-reply"></i> {{ __('Send a Reply') }}</h5>
                                    <form action="{{ Route::has('merchant.support-tickets.reply') ? route('merchant.support-tickets.reply', $ticket->id) : '#' }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <textarea name="reply_text" rows="4" class="form-control" placeholder="{{ __('Write your message here...') }}" required></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="file" name="attachments[]" class="form-control form-control-sm" multiple accept="image/*,.pdf" style="max-width: 250px;">
                                                <small class="text-muted"><i class="las la-paperclip"></i> JPG, PNG, PDF</small>
                                            </div>
                                            <button type="submit" class="btn sg-btn-primary d-flex align-items-center gap-2 px-4">
                                                <i class="las la-paper-plane"></i>
                                                <span>{{ __('Send Reply') }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-secondary text-center mb-0">
                                    <i class="las la-lock"></i> {{ __('This ticket has been closed. You cannot reply to a closed ticket.') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Ticket Metadata -->
                    <div class="col-lg-4">
                        <div class="card bg-white redious-border p-20 p-sm-30 mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom">{{ __('Ticket Information') }}</h5>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Status') }}:</td>
                                    <td class="text-end pe-0">
                                        @php
                                            $statusBadges = [
                                                'new'        => 'bg-primary text-white',
                                                'processing' => 'bg-warning text-dark',
                                                'resolved'   => 'bg-success text-white',
                                                'closed'     => 'bg-dark text-white',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusBadges[$ticket->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Priority') }}:</td>
                                    <td class="text-end pe-0">
                                        @php
                                            $priorityBadges = [
                                                'low'    => 'bg-info text-white',
                                                'medium' => 'bg-primary text-white',
                                                'high'   => 'bg-warning text-dark',
                                                'urgent' => 'bg-danger text-white',
                                            ];
                                        @endphp
                                        <span class="badge {{ $priorityBadges[$ticket->priority] ?? 'bg-secondary' }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Issue Type') }}:</td>
                                    <td class="text-end pe-0 fw-semibold">{{ ucwords(str_replace('_', ' ', $ticket->ticket_type)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Created At') }}:</td>
                                    <td class="text-end pe-0">{{ $ticket->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @if($ticket->resolved_at)
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Resolved At') }}:</td>
                                    <td class="text-end pe-0 text-success">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Parcel Information (If attached) -->
                        @if($ticket->tracking_number || $ticket->parcel)
                            <div class="card bg-white redious-border p-20 p-sm-30 mb-4">
                                <h5 class="fw-bold mb-3 pb-2 border-bottom"><i class="las la-box"></i> {{ __('Parcel Reference') }}</h5>
                                <div class="mb-2">
                                    <span class="text-muted">{{ __('Tracking ID') }}:</span>
                                    <span class="badge bg-secondary text-white ms-1">{{ $ticket->tracking_number ?? @$ticket->parcel->parcel_no }}</span>
                                </div>
                                @if($ticket->parcel)
                                    <div class="mb-2">
                                        <span class="text-muted">{{ __('Customer') }}:</span>
                                        <strong>{{ $ticket->parcel->customer_name }}</strong> ({{ $ticket->parcel->customer_phone_number }})
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted">{{ __('Parcel Status') }}:</span>
                                        <span class="badge bg-info text-white">{{ __($ticket->parcel->status) }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted">{{ __('Total Price') }}:</span>
                                        <strong>{{ format_price($ticket->parcel->total_delivery_charge) }}</strong>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ Route::has('merchant.parcel.detail') ? route('merchant.parcel.detail', $ticket->parcel->id) : '#' }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="las la-external-link-alt"></i> {{ __('View Full Parcel Details') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
