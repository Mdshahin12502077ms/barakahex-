@extends('backend.layouts.master')

@section('title')
    {{ __('deliveries') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
        <div>
            <h4 class="section-title mb-1">{{ __('my_deliveries') }}</h4>
            <p class="text-muted small mb-0">{{ __('manage_and_track_assigned_customer_deliveries') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveryman.dashboard') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-arrow-left"></i> {{ __('back_to_dashboard') }}
            </a>
        </div>
    </div>

    {{-- Statistics Counters --}}
    <div class="row">
        {{-- Processing Deliveries --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-2">
                    <div class="analytics-icon">
                        <i class="las la-shipping-fast"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['processing'] ?? 0 }}</h4>
                        <p>{{ __('processing_deliveries') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completed Deliveries --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-1">
                    <div class="analytics-icon">
                        <i class="las la-check-circle"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['completed'] ?? 0 }}</h4>
                        <p>{{ __('completed_deliveries') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rescheduled Deliveries --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-3">
                    <div class="analytics-icon">
                        <i class="las la-history"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['rescheduled'] ?? 0 }}</h4>
                        <p>{{ __('rescheduled_deliveries') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Deliveries --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-5">
                    <div class="analytics-icon">
                        <i class="las la-boxes"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['total'] ?? 0 }}</h4>
                        <p>{{ __('total_assigned') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white redious-border p-20 p-sm-30 mb-4">
        <form action="{{ route('deliveryman.deliveries') }}" method="GET" class="mb-20">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="{{ __('search_by_parcel_customer_or_phone') }}" 
                               value="{{ request('search') }}">
                        <button class="btn sg-btn-primary" type="submit">
                            <i class="las la-search"></i> {{ __('search') }}
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                        <a href="{{ route('deliveryman.deliveries') }}" 
                           class="btn btn-sm {{ !request('status') || request('status') == 'all' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('all') }}
                        </a>
                        <a href="{{ route('deliveryman.deliveries', ['status' => 'processing']) }}" 
                           class="btn btn-sm {{ request('status') == 'processing' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('processing') }}
                        </a>
                        <a href="{{ route('deliveryman.deliveries', ['status' => 'completed']) }}" 
                           class="btn btn-sm {{ request('status') == 'completed' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('delivered') }}
                        </a>
                        <a href="{{ route('deliveryman.deliveries', ['status' => 'rescheduled']) }}" 
                           class="btn btn-sm {{ request('status') == 'rescheduled' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('rescheduled') }}
                        </a>
                        <a href="{{ route('deliveryman.deliveries', ['status' => 'cancelled']) }}" 
                           class="btn btn-sm {{ request('status') == 'cancelled' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('cancelled') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Deliveries Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('parcel_no') }}</th>
                        <th>{{ __('customer') }}</th>
                        <th>{{ __('phone') }}</th>
                        <th>{{ __('delivery_address') }}</th>
                        <th>{{ __('cod_amount') }}</th>
                        <th>{{ __('status') }}</th>
                        <th class="text-end">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($deliveries as $parcel)
                        <tr>
                            <td>
                                <a href="{{ route('deliveryman.parcel.detail', $parcel->id) }}" class="fw-bold text-primary">
                                    {{ $parcel->parcel_no }}
                                </a>
                                @if($parcel->customer_invoice_no)
                                    <div class="small text-muted">
                                        #{{ $parcel->customer_invoice_no }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $parcel->customer_name }}</div>
                                <small class="text-muted">
                                    {{ $parcel->merchant->company_name ?? ($parcel->merchant->user->first_name ?? '') }}
                                </small>
                            </td>
                            <td>
                                @if($parcel->customer_phone_number)
                                    <a href="tel:{{ $parcel->customer_phone_number }}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1">
                                        <i class="las la-phone"></i> {{ $parcel->customer_phone_number }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-truncate d-inline-block" style="max-width: 170px;" title="{{ $parcel->customer_address }}">
                                        {{ $parcel->customer_address ?: 'N/A' }}
                                    </span>
                                    @if($parcel->customer_address)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($parcel->customer_address) }}" 
                                           target="_blank" 
                                           class="text-danger" 
                                           title="{{ __('open_in_google_maps') }}">
                                            <i class="las la-map-marked-alt font-18"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ setting('default_currency') }} {{ number_format($parcel->price, 2) }}</span>
                                <div class="small text-muted">{{ $parcel->weight }} KG</div>
                            </td>
                            <td>
                                @if($parcel->status == 'delivered-and-verified')
                                    <span class="badge bg-success text-white">
                                        <i class="las la-check-double"></i> {{ __('verified_delivered') }}
                                    </span>
                                @elseif($parcel->status == 'delivered')
                                    <span class="badge bg-primary text-white">
                                        <i class="las la-check"></i> {{ __('delivered') }}
                                    </span>
                                @elseif(in_array($parcel->status, ['re-schedule-delivery', 'delivery_re_schedule']))
                                    <span class="badge bg-warning text-dark">
                                        <i class="las la-clock"></i> {{ __('rescheduled') }}
                                    </span>
                                @elseif(in_array($parcel->status, ['cancel', 'cancelled']))
                                    <span class="badge bg-danger text-white">
                                        <i class="las la-times-circle"></i> {{ __('cancelled') }}
                                    </span>
                                @else
                                    <span class="badge bg-info text-white">
                                        <i class="las la-truck"></i> {{ __('processing') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if(!in_array($parcel->status, ['delivered', 'delivered-and-verified', 'cancel', 'cancelled']))
                                        {{-- Mark Delivered Form --}}
                                        <form action="{{ route('deliveryman.delivered', $parcel->id) }}" method="POST" class="d-inline confirm-form"
                                              data-title="{{ __('are_you_sure') }}"
                                              data-text="{{ __('confirm_parcel_delivered_and_cod_collected') }}"
                                              data-confirm-btn="{{ __('yes_delivered') ?? __('delivered') }}"
                                              data-cancel-btn="{{ __('cancel') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="{{ __('mark_delivered') }}">
                                                <i class="las la-check"></i> {{ __('delivered') }}
                                            </button>
                                        </form>

                                        {{-- Reschedule Modal Button --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rescheduleDeliveryModal{{ $parcel->id }}"
                                                title="{{ __('reschedule') }}">
                                            <i class="las la-calendar-plus"></i>
                                        </button>

                                        {{-- Cancel Modal Button --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelDeliveryModal{{ $parcel->id }}"
                                                title="{{ __('cancel') }}">
                                            <i class="las la-times"></i>
                                        </button>
                                    @elseif($parcel->status == 'delivered')
                                        {{-- OTP Verify Button --}}
                                        <button type="button" class="btn btn-sm btn-warning text-dark"
                                                data-bs-toggle="modal"
                                                data-bs-target="#otpModal{{ $parcel->id }}"
                                                title="{{ __('verify_otp') }}">
                                            <i class="las la-shield-alt"></i> {{ __('verify_otp') }}
                                        </button>
                                    @endif

                                    {{-- Details Link --}}
                                    <a href="{{ route('deliveryman.parcel.detail', $parcel->id) }}" class="btn btn-sm sg-btn-outline-primary" title="{{ __('view_details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                </div>

                                {{-- OTP Verify Modal --}}
                                <div class="modal fade" id="otpModal{{ $parcel->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('verify_delivery_otp') }} - #{{ $parcel->parcel_no }}</h5>
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

                                                <form action="{{ route('deliveryman.verify.otp', $parcel->id) }}" method="POST" id="otpVerifyForm{{ $parcel->id }}">
                                                    @csrf
                                                    <p class="text-muted small mb-3">{{ __('enter_4_digit_otp_sent_to_customer') }}</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('otp_code') }} <span class="text-danger">*</span></label>
                                                        <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold letter-spacing-2" required maxlength="10" placeholder="1234" {{ ($parcel->otp_attempts >= 3) ? 'disabled' : '' }}>
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
                                                        <button type="button" class="btn btn-success" onclick="document.getElementById('otpVerifyForm{{ $parcel->id }}').submit();">{{ __('confirm_verification') }}</button>
                                                    @else
                                                        <button type="button" class="btn btn-secondary" disabled title="{{ __('max_wrong_otp_attempts_reached_please_resend') }}">{{ __('confirm_verification') }}</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Reschedule Modal --}}
                                <div class="modal fade" id="rescheduleDeliveryModal{{ $parcel->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="{{ route('deliveryman.reschedule', $parcel->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('reschedule_delivery') }} - #{{ $parcel->parcel_no }}</h5>
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

                                {{-- Cancel Modal --}}
                                <div class="modal fade" id="cancelDeliveryModal{{ $parcel->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="{{ route('deliveryman.cancel', $parcel->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger">{{ __('cancel_delivery') }} - #{{ $parcel->parcel_no }}</h5>
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
                                                    <button type="submit" class="btn btn-danger">{{ __('confirm_cancellation') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="las la-box-open font-40 d-block mb-2 text-muted"></i>
                                <h5>{{ __('no_deliveries_found') }}</h5>
                                <p class="small mb-0">{{ __('you_do_not_have_any_deliveries_matching_the_selected_criteria') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($deliveries->hasPages())
            <div class="mt-20 d-flex justify-content-end">
                {!! $deliveries->withQueryString()->links() !!}
            </div>
        @endif
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
