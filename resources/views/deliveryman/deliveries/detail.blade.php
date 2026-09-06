@extends('backend.layouts.master')

@section('title')
    {{ __('parcel_details') }} - #{{ $parcel->parcel_no }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
        <div>
            <h4 class="section-title mb-1">{{ __('parcel_details') }} #{{ $parcel->parcel_no }}</h4>
            <p class="text-muted small mb-0">{{ __('view_tracking_timeline_and_manage_actions') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveryman.deliveries') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-arrow-left"></i> {{ __('back_to_deliveries') }}
            </a>
            <a href="{{ route('deliveryman.pickups') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-truck-pickup"></i> {{ __('my_pickups') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Parcel Summary & Timeline --}}
        <div class="col-lg-8">
            {{-- Parcel Overview Card --}}
            <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="las la-box font-20 text-primary"></i> {{ __('parcel_information') }}
                    </h5>
                    <div>
                        @if($parcel->status == 'delivered-and-verified')
                            <span class="badge bg-success text-white py-2 px-3">
                                <i class="las la-check-double"></i> {{ __('verified_delivered') }}
                            </span>
                        @elseif($parcel->status == 'delivered')
                            <span class="badge bg-primary text-white py-2 px-3">
                                <i class="las la-check"></i> {{ __('delivered') }}
                            </span>
                        @elseif(in_array($parcel->status, ['re-schedule-delivery', 'delivery_re_schedule']))
                            <span class="badge bg-warning text-dark py-2 px-3">
                                <i class="las la-clock"></i> {{ __('rescheduled') }}
                            </span>
                        @elseif(in_array($parcel->status, ['cancel', 'cancelled']))
                            <span class="badge bg-danger text-white py-2 px-3">
                                <i class="las la-times-circle"></i> {{ __('cancelled') }}
                            </span>
                        @else
                            <span class="badge bg-info text-white py-2 px-3">
                                <i class="las la-truck"></i> {{ __('processing') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row g-3 py-2 border-top">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('parcel_no') }}</div>
                        <div class="fw-bold text-primary">#{{ $parcel->parcel_no }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('invoice_no') }}</div>
                        <div class="fw-bold">{{ $parcel->customer_invoice_no ?: 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('cod_amount') }}</div>
                        <div class="fw-bold text-success font-16">{{ setting('default_currency') }} {{ number_format($parcel->price, 2) }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('weight') }}</div>
                        <div class="fw-bold">{{ $parcel->weight }} KG</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('delivery_charge') }}</div>
                        <div class="fw-bold">{{ setting('default_currency') }} {{ number_format($parcel->total_delivery_charge, 2) }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('created_at') }}</div>
                        <div>{{ date('d M Y, h:i A', strtotime($parcel->created_at)) }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('pickup_date') }}</div>
                        <div>{{ $parcel->pickup_date ? date('d M Y', strtotime($parcel->pickup_date)) : 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small">{{ __('delivery_date') }}</div>
                        <div>{{ $parcel->delivery_date ? date('d M Y', strtotime($parcel->delivery_date)) : 'N/A' }}</div>
                    </div>
                </div>

                @if($parcel->note)
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>{{ __('note') }}:</strong> {{ $parcel->note }}
                    </div>
                @endif
            </div>

            {{-- Timeline History Card --}}
            <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                <h5 class="fw-bold text-dark mb-4">
                    <i class="las la-history font-20 text-warning"></i> {{ __('tracking_timeline') }}
                </h5>

                <div class="timeline-wrapper">
                    @forelse ($parcel->events ?? [] as $event)
                        <div class="d-flex mb-4 position-relative">
                            <div class="me-3 text-center" style="min-width: 40px;">
                                <div class="badge rounded-circle bg-primary text-white p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="las la-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="fw-bold text-dark mb-1">{{ __(str_replace('_', ' ', $event->title)) }}</h6>
                                    <small class="text-muted">{{ date('d M Y, h:i A', strtotime($event->created_at)) }}</small>
                                </div>
                                @if($event->cancel_note)
                                    <p class="mb-1 text-secondary small">{{ $event->cancel_note }}</p>
                                @endif
                                <div class="small text-muted">
                                    {{ __('performed_by') }}: 
                                    <strong>{{ @$event->user->first_name }} {{ @$event->user->last_name }}</strong>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">{{ __('no_events_found') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- OTP Verification History Card --}}
            @if(isset($parcel->otpLogs) && $parcel->otpLogs->isNotEmpty())
                <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="las la-shield-alt font-20 text-primary"></i> {{ __('otp_verification_history') ?? 'OTP Verification History' }}
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-start mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('time') }}</th>
                                    <th>{{ __('action') }}</th>
                                    <th>{{ __('input_code') ?? 'Input Code' }}</th>
                                    <th>{{ __('attempt') ?? 'Attempt' }}</th>
                                    <th>{{ __('status_message') ?? 'Message' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parcel->otpLogs as $log)
                                    <tr>
                                        <td class="small text-muted" style="white-space: nowrap;">
                                            {{ date('d M Y, h:i:s A', strtotime($log->created_at)) }}
                                        </td>
                                        <td>
                                            @if($log->action == 'success')
                                                <span class="badge bg-success"><i class="las la-check-circle"></i> {{ __('success') }}</span>
                                            @elseif($log->action == 'wrong_attempt')
                                                <span class="badge bg-warning text-dark"><i class="las la-exclamation-circle"></i> {{ __('wrong_attempt') ?? 'Wrong Attempt' }}</span>
                                            @elseif($log->action == 'locked')
                                                <span class="badge bg-danger"><i class="las la-lock"></i> {{ __('locked') }}</span>
                                            @elseif($log->action == 'expired')
                                                <span class="badge bg-secondary"><i class="las la-clock"></i> {{ __('expired') }}</span>
                                            @elseif($log->action == 'resend')
                                                <span class="badge bg-info text-white"><i class="las la-redo"></i> {{ __('resend') }}</span>
                                            @else
                                                <span class="badge bg-primary"><i class="las la-paper-plane"></i> {{ __('generated') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->submitted_otp)
                                                <code class="fw-bold">{{ $log->submitted_otp }}</code>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->attempt_number > 0)
                                                <span class="badge bg-light text-dark border">{{ $log->attempt_number }}/3</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ $log->status_message ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Customer & Merchant Info --}}
        <div class="col-lg-4">
            {{-- Customer Card --}}
            <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="las la-user-tag font-20 text-success"></i> {{ __('customer_info') }}
                </h5>
                <div class="mb-3">
                    <div class="fw-bold font-16 text-dark">{{ $parcel->customer_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">{{ __('phone_number') }}</div>
                    @if($parcel->customer_phone_number)
                        <a href="tel:{{ $parcel->customer_phone_number }}" class="btn btn-outline-success btn-sm w-100 py-2">
                            <i class="las la-phone"></i> {{ $parcel->customer_phone_number }}
                        </a>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">{{ __('delivery_address') }}</div>
                    <p class="mb-2 text-dark">{{ $parcel->customer_address ?: 'N/A' }}</p>
                    @if($parcel->customer_address)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($parcel->customer_address) }}" 
                           target="_blank" 
                           class="btn btn-danger btn-sm w-100 py-2">
                            <i class="las la-map-marked-alt"></i> {{ __('navigate_with_google_maps') }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Merchant Card --}}
            <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="las la-store font-20 text-info"></i> {{ __('merchant_info') }}
                </h5>
                <div class="mb-2">
                    <div class="fw-bold text-dark">{{ $parcel->shop->name ?? ($parcel->merchant->company_name ?? 'N/A') }}</div>
                    <small class="text-muted">{{ $parcel->merchant->user->first_name ?? '' }} {{ $parcel->merchant->user->last_name ?? '' }}</small>
                </div>
                @php
                    $mPhone = $parcel->shop->contact_number ?? ($parcel->merchant->phone_number ?? ($parcel->merchant->user->phone_number ?? ''));
                @endphp
                @if($mPhone)
                    <div class="mb-3">
                        <a href="tel:{{ $mPhone }}" class="btn btn-outline-info btn-sm w-100 py-2">
                            <i class="las la-phone"></i> {{ $mPhone }}
                        </a>
                    </div>
                @endif
                @php
                    $mAddr = $parcel->shop->address ?? ($parcel->merchant->address ?? '');
                @endphp
                @if($mAddr)
                    <div class="small text-muted">
                        <i class="las la-map-pin"></i> {{ $mAddr }}
                    </div>
                @endif
            </div>

            {{-- Action Buttons Card --}}
            @if(!in_array($parcel->status, ['delivered-and-verified', 'cancel', 'cancelled']))
                <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                    <h5 class="fw-bold text-dark mb-3">{{ __('actions') }}</h5>
                    <div class="d-grid gap-2">
                        @if(in_array($parcel->status, ['pickup_assigned', 'pickup-assigned', 'pickup_re_schedule', 're-schedule-pickup']))
                            {{-- Pickup Actions --}}
                            <form action="{{ route('deliveryman.pickup.received', $parcel->id) }}" method="POST" class="confirm-form"
                                  data-title="{{ __('are_you_sure') }}"
                                  data-text="{{ __('are_you_sure_you_received_this_parcel') }}"
                                  data-confirm-btn="{{ __('yes_receive') ?? __('receive') }}"
                                  data-cancel-btn="{{ __('cancel') }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100 py-2">
                                    <i class="las la-truck-loading"></i> {{ __('confirm_pickup_received') ?? __('receive') }}
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-warning w-100 py-2" data-bs-toggle="modal" data-bs-target="#reschedulePickupModal">
                                <i class="las la-calendar-plus"></i> {{ __('reschedule_pickup') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger w-100 py-2" data-bs-toggle="modal" data-bs-target="#cancelPickupModal">
                                <i class="las la-times-circle"></i> {{ __('cancel_pickup') }}
                            </button>
                        @elseif($parcel->status != 'delivered')
                            {{-- Delivery Actions --}}
                            <form action="{{ route('deliveryman.delivered', $parcel->id) }}" method="POST" class="confirm-form"
                                  data-title="{{ __('are_you_sure') }}"
                                  data-text="{{ __('confirm_parcel_delivered_and_cod_collected') }}"
                                  data-confirm-btn="{{ __('yes_delivered') ?? __('delivered') }}"
                                  data-cancel-btn="{{ __('cancel') }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100 py-2">
                                    <i class="las la-check-circle"></i> {{ __('mark_as_delivered') }}
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-warning w-100 py-2" data-bs-toggle="modal" data-bs-target="#rescheduleDeliveryModal">
                                <i class="las la-calendar-plus"></i> {{ __('reschedule_delivery') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger w-100 py-2" data-bs-toggle="modal" data-bs-target="#cancelDeliveryModal">
                                <i class="las la-times-circle"></i> {{ __('cancel_delivery') }}
                            </button>
                        @elseif($parcel->status == 'delivered')
                            {{-- OTP Verification Action --}}
                            <button type="button" class="btn btn-warning btn-lg w-100 py-2 text-dark"
                                    data-bs-toggle="modal" data-bs-target="#detailOtpModal">
                                <i class="las la-shield-alt"></i> {{ __('verify_customer_otp') }}
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Reschedule Pickup Modal --}}
                <div class="modal fade" id="reschedulePickupModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-start">
                            <form action="{{ route('deliveryman.pickup.reschedule', $parcel->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('reschedule_pickup') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('new_pickup_date') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="pickup_date" class="form-control" required min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('note') }}</label>
                                        <textarea name="note" class="form-control" rows="3" placeholder="{{ __('reason_for_rescheduling') }}"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('close') }}</button>
                                    <button type="submit" class="btn sg-btn-primary">{{ __('save_changes') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Cancel Pickup Modal --}}
                <div class="modal fade" id="cancelPickupModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-start">
                            <form action="{{ route('deliveryman.pickup.cancel', $parcel->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('cancel_pickup') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('cancellation_reason') }} <span class="text-danger">*</span></label>
                                        <textarea name="note" class="form-control" rows="3" required placeholder="{{ __('enter_why_pickup_is_cancelled') }}"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('close') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('confirm_cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Reschedule Delivery Modal --}}
                <div class="modal fade" id="rescheduleDeliveryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-start">
                            <form action="{{ route('deliveryman.reschedule', $parcel->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('reschedule_delivery') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('new_delivery_date') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="delivery_date" class="form-control" required min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('reason') }} <span class="text-danger">*</span></label>
                                        <select name="note" class="form-select reason-select" required onchange="handleReasonChange(this)">
                                            <option value="">-- {{ __('select_reason') }} --</option>
                                            <option value="Customer Unavailable">{{ __('customer_unavailable') }}</option>
                                            <option value="Phone Unreachable">{{ __('phone_unreachable') }}</option>
                                            <option value="Wrong Address">{{ __('wrong_address') }}</option>
                                            <option value="Customer Refused">{{ __('customer_refused') }}</option>
                                            <option value="Shop Closed">{{ __('shop_closed') }}</option>
                                            <option value="Cash Problem">{{ __('cash_problem') }}</option>
                                            <option value="Other">{{ __('other') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 custom-note-container d-none">
                                        <label class="form-label">{{ __('specify_other_reason') }} <span class="text-danger">*</span></label>
                                        <textarea name="custom_note" class="form-control" rows="3" placeholder="{{ __('enter_custom_reason') }}"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('close') }}</button>
                                    <button type="submit" class="btn sg-btn-primary">{{ __('save_changes') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Cancel Delivery Modal --}}
                <div class="modal fade" id="cancelDeliveryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-start">
                            <form action="{{ route('deliveryman.cancel', $parcel->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger">{{ __('cancel_delivery') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('cancellation_reason') }} <span class="text-danger">*</span></label>
                                        <select name="note" class="form-select reason-select" required onchange="handleReasonChange(this)">
                                            <option value="">-- {{ __('select_reason') }} --</option>
                                            <option value="Customer Unavailable">{{ __('customer_unavailable') }}</option>
                                            <option value="Phone Unreachable">{{ __('phone_unreachable') }}</option>
                                            <option value="Wrong Address">{{ __('wrong_address') }}</option>
                                            <option value="Customer Refused">{{ __('customer_refused') }}</option>
                                            <option value="Shop Closed">{{ __('shop_closed') }}</option>
                                            <option value="Cash Problem">{{ __('cash_problem') }}</option>
                                            <option value="Other">{{ __('other') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 custom-note-container d-none">
                                        <label class="form-label">{{ __('specify_other_reason') }} <span class="text-danger">*</span></label>
                                        <textarea name="custom_note" class="form-control" rows="3" placeholder="{{ __('enter_custom_reason') }}"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('close') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('confirm_cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- OTP Modal --}}
                <div class="modal fade" id="detailOtpModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-start">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('verify_delivery_otp') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if(!empty($parcel->otp_attempts) && $parcel->otp_attempts >= 3)
                                    <div class="alert alert-danger py-2 small mb-3">
                                        <i class="las la-lock font-18"></i> <strong>{{ __('otp_locked') }}:</strong> {{ __('max_wrong_otp_attempts_reached_please_resend') }}
                                    </div>
                                @elseif(!empty($parcel->otp_attempts) && $parcel->otp_attempts > 0)
                                    <div class="alert alert-warning py-2 small mb-3 d-flex justify-content-between align-items-center">
                                        <span><i class="las la-exclamation-circle"></i> {{ __('wrong_attempts') }}: <strong>{{ $parcel->otp_attempts }}/3</strong></span>
                                        <span class="badge bg-warning text-dark">{{ 3 - $parcel->otp_attempts }} {{ __('attempts_left') }}</span>
                                    </div>
                                @endif

                                @if(!empty($parcel->otp_expired_at))
                                    @if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($parcel->otp_expired_at)))
                                        <div class="alert alert-danger py-2 small mb-3">
                                            <i class="las la-exclamation-triangle"></i> {{ __('otp_has_been_expired_please_resend') }}
                                        </div>
                                    @else
                                        <div class="alert alert-info py-2 small mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="las la-clock"></i> {{ __('otp_valid_until') }}: <strong>{{ \Carbon\Carbon::parse($parcel->otp_expired_at)->format('h:i:s A') }}</strong></span>
                                            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($parcel->otp_expired_at)->diffForHumans(['parts' => 1]) }}</span>
                                        </div>
                                    @endif
                                @endif

                                <form action="{{ route('deliveryman.verify.otp', $parcel->id) }}" method="POST" id="detailOtpVerifyForm">
                                    @csrf
                                    <p class="text-muted small mb-3">{{ __('enter_4_digit_otp_sent_to_customer') }}</p>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('otp_code') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold" required maxlength="10" placeholder="1234" {{ ($parcel->otp_attempts >= 3) ? 'disabled' : '' }}>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer d-flex justify-content-between">
                                <form action="{{ route('deliveryman.resend.otp', $parcel->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm">
                                        <i class="las la-redo"></i> {{ __('resend_otp') }}
                                    </button>
                                </form>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('close') }}</button>
                                    @if(($parcel->otp_attempts ?? 0) < 3)
                                        <button type="button" class="btn btn-success" onclick="document.getElementById('detailOtpVerifyForm').submit();">{{ __('confirm_verification') }}</button>
                                    @else
                                        <button type="button" class="btn btn-secondary" disabled title="{{ __('max_wrong_otp_attempts_reached_please_resend') }}">{{ __('confirm_verification') }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).on('submit', '.confirm-form', function(e) {
        e.preventDefault();
        var form = this;
        var title = $(form).data('title') || "{{ __('are_you_sure') }}";
        var text = $(form).data('text') || "{{ __('you_won_t_be_able_to_revert_this') }}";
        var confirmBtnText = $(form).data('confirm-btn') || "{{ __('yes_i_m') }}";
        var cancelBtnText = $(form).data('cancel-btn') || "{{ __('cancel') }}";

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmBtnText,
            cancelButtonText: cancelBtnText
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    function handleReasonChange(selectElem) {
        var $form = $(selectElem).closest('form');
        var $container = $form.find('.custom-note-container');
        var $textarea = $container.find('textarea');
        if ($(selectElem).val() === 'Other') {
            $container.removeClass('d-none');
            $textarea.prop('required', true);
        } else {
            $container.addClass('d-none');
            $textarea.prop('required', false);
        }
    }
</script>
@endpush
