@extends('backend.layouts.master')

@section('title')
    {{ __('pickups') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
        <div>
            <h4 class="section-title mb-1">{{ __('my_pickups') }}</h4>
            <p class="text-muted small mb-0">{{ __('manage_and_track_assigned_merchant_pickups') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveryman.dashboard') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-arrow-left"></i> {{ __('back_to_dashboard') }}
            </a>
        </div>
    </div>

    {{-- Statistics Counters --}}
    <div class="row">
        {{-- Pending Pickups --}}
        <div class="col-xxl-3 col-xl-3 col-md-4">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-3">
                    <div class="analytics-icon">
                        <i class="las la-truck-pickup"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['pending'] ?? 0 }}</h4>
                        <p>{{ __('pending_pickups') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completed Pickups --}}
        <div class="col-xxl-3 col-xl-3 col-md-4">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-1">
                    <div class="analytics-icon">
                        <i class="las la-check-circle"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['completed'] ?? 0 }}</h4>
                        <p>{{ __('completed_pickups') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rescheduled Pickups --}}
        <div class="col-xxl-3 col-xl-3 col-md-4">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-2">
                    <div class="analytics-icon">
                        <i class="las la-history"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ $statistics['reschedule'] ?? 0 }}</h4>
                        <p>{{ __('rescheduled_pickups') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pickups --}}
        <div class="col-xxl-3 col-xl-3 col-md-4">
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
        <form action="{{ route('deliveryman.pickups') }}" method="GET" class="mb-20">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="{{ __('search_by_parcel_merchant_or_phone') }}" 
                               value="{{ request('search') }}">
                        <button class="btn sg-btn-primary" type="submit">
                            <i class="las la-search"></i> {{ __('search') }}
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                        <a href="{{ route('deliveryman.pickups') }}" 
                           class="btn btn-sm {{ !request('status') || request('status') == 'all' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('all') }}
                        </a>
                        <a href="{{ route('deliveryman.pickups', ['status' => 'pickup_assigned']) }}" 
                           class="btn btn-sm {{ request('status') == 'pickup_assigned' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('pending') }}
                        </a>
                        <a href="{{ route('deliveryman.pickups', ['status' => 'received-by-pickup-man']) }}" 
                           class="btn btn-sm {{ request('status') == 'received-by-pickup-man' || request('status') == 'pickup_received' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('completed') }}
                        </a>
                        <a href="{{ route('deliveryman.pickups', ['status' => 're-schedule-pickup']) }}" 
                           class="btn btn-sm {{ request('status') == 're-schedule-pickup' || request('status') == 'pickup_re_schedule' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('rescheduled') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Pickups Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('parcel_no') }}</th>
                        <th>{{ __('merchant_shop') }}</th>
                        <th>{{ __('pickup_phone') }}</th>
                        <th>{{ __('pickup_address') }}</th>
                        <th>{{ __('amount') }}</th>
                        <th>{{ __('status') }}</th>
                        <th class="text-end">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pickups as $parcel)
                        <tr>
                            <td>
                                <a href="{{ route('deliveryman.parcel.detail', $parcel->id) }}" class="fw-bold text-primary">
                                    {{ $parcel->parcel_no }}
                                </a>
                                @if($parcel->pickup_date)
                                    <div class="small text-muted">
                                        <i class="las la-calendar"></i> {{ date('d M Y', strtotime($parcel->pickup_date)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $parcel->shop->name ?? ($parcel->merchant->company_name ?? 'N/A') }}
                                </div>
                                <small class="text-muted">
                                    {{ $parcel->merchant->user->first_name ?? '' }} {{ $parcel->merchant->user->last_name ?? '' }}
                                </small>
                            </td>
                            <td>
                                @php
                                    $pickupPhone = $parcel->shop->contact_number ?? ($parcel->merchant->phone_number ?? ($parcel->merchant->user->phone_number ?? ''));
                                @endphp
                                @if($pickupPhone)
                                    <a href="tel:{{ $pickupPhone }}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1">
                                        <i class="las la-phone"></i> {{ $pickupPhone }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $pickupAddr = $parcel->shop->address ?? ($parcel->merchant->address ?? ($parcel->pickup_address ?? ''));
                                @endphp
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-truncate d-inline-block" style="max-width: 160px;" title="{{ $pickupAddr }}">
                                        {{ $pickupAddr ?: 'N/A' }}
                                    </span>
                                    @if($pickupAddr)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($pickupAddr) }}" 
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
                                @if(in_array($parcel->status, ['pickup_received', 'received-by-pickup-man']))
                                    <span class="badge bg-success text-white">
                                        <i class="las la-check-circle"></i> {{ __('received') }}
                                    </span>
                                @elseif(in_array($parcel->status, ['pickup_re_schedule', 're-schedule-pickup']))
                                    <span class="badge bg-warning text-dark">
                                        <i class="las la-clock"></i> {{ __('rescheduled') }}
                                    </span>
                                @elseif($parcel->status == 'cancel')
                                    <span class="badge bg-danger text-white">
                                        <i class="las la-times-circle"></i> {{ __('cancelled') }}
                                    </span>
                                @else
                                    <span class="badge bg-info text-white">
                                        <i class="las la-spinner"></i> {{ __('pending_pickup') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if(!in_array($parcel->status, ['pickup_received', 'received-by-pickup-man', 'cancel']))
                                        {{-- Pickup Confirm Form --}}
                                        <form action="{{ route('deliveryman.pickup.received', $parcel->id) }}" method="POST" class="d-inline confirm-form"
                                              data-title="{{ __('are_you_sure') }}"
                                              data-text="{{ __('are_you_sure_you_received_this_parcel') }}"
                                              data-confirm-btn="{{ __('yes_receive') ?? __('receive') }}"
                                              data-cancel-btn="{{ __('cancel') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="{{ __('confirm_pickup') }}">
                                                <i class="las la-check"></i> {{ __('receive') }}
                                            </button>
                                        </form>

                                        {{-- Reschedule Button (Opens Modal) --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rescheduleModal{{ $parcel->id }}"
                                                title="{{ __('reschedule') }}">
                                            <i class="las la-calendar-plus"></i>
                                        </button>

                                        {{-- Cancel Button (Opens Modal) --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelModal{{ $parcel->id }}"
                                                title="{{ __('cancel') }}">
                                            <i class="las la-times"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('deliveryman.parcel.detail', $parcel->id) }}" class="btn btn-sm sg-btn-outline-primary">
                                            <i class="las la-eye"></i> {{ __('view') }}
                                        </a>
                                    @endif
                                </div>

                                {{-- Reschedule Modal --}}
                                <div class="modal fade" id="rescheduleModal{{ $parcel->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="{{ route('deliveryman.pickup.reschedule', $parcel->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('reschedule_pickup') }} - #{{ $parcel->parcel_no }}</h5>
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

                                {{-- Cancel Modal --}}
                                <div class="modal fade" id="cancelModal{{ $parcel->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="{{ route('deliveryman.pickup.cancel', $parcel->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger">{{ __('cancel_pickup') }} - #{{ $parcel->parcel_no }}</h5>
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
                                <i class="las la-truck-loading font-40 d-block mb-2 text-muted"></i>
                                <h5>{{ __('no_pickups_found') }}</h5>
                                <p class="small mb-0">{{ __('you_do_not_have_any_pickups_matching_the_selected_criteria') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pickups->hasPages())
            <div class="mt-20 d-flex justify-content-end">
                {!! $pickups->withQueryString()->links() !!}
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
</script>
@endpush
