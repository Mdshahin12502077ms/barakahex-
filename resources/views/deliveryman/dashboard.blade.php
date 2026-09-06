@extends('backend.layouts.master')

@section('title')
    {{ __('rider_dashboard') }}
@endsection

@section('mainContent')
    @if(isset($notices) && count($notices) > 0)
        @foreach ($notices as $notice)
            <div class="example-alert mb-3">
                <div class="alert {{ $notice->alert_class }} alert-icon alert-dismissible">
                    <i class="icon las la-exclamation-circle"></i>
                    {{ $notice->details }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endforeach
    @endif

    <div class="container-fluid">
        <div class="row">
            {{-- Rider Profile & COD Cash Card --}}
            <div class="col-xxl-3 col-xl-4 col-md-12">
                <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="analytics-content mb-1">
                                <h4 class="no-line-braek">{{ __('hello') }}, {{ $user->first_name . ' ' . $user->last_name }} 👋</h4>
                                <p class="text-muted small mb-0">{{ __('welcome_to_your_delivery_panel') }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="badge bg-primary rounded-pill px-3 py-1">
                                    <i class="las la-motorcycle"></i> {{ __('rider') }}
                                </span>
                                @if($deliveryMan->user && $deliveryMan->user->branch)
                                    <span class="badge bg-light text-dark border">
                                        <i class="las la-map-marker-alt"></i> {{ $deliveryMan->user->branch->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="profit clr-1 p-3 rounded-3 mb-3">
                                <div class="profit-icon p-3">
                                    <i class="las la-hand-holding-usd text-primary font-28"></i>
                                </div>
                                <div class="profit-content no-line-braek">
                                    <h3 class="own-balance mb-0">{{ setting('default_currency') }} {{ number_format($cash_in_hand, 2) }}</h3>
                                    <p class="mb-0 text-muted">{{ __('cash_in_hand') }} (COD)</p>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('deliveryman.deliveries') }}" class="btn sg-btn-primary">
                                    <i class="las la-shipping-fast"></i> {{ __('start_deliveries') }}
                                </a>
                                <a href="{{ route('deliveryman.pickups') }}" class="btn sg-btn-outline-primary">
                                    <i class="las la-truck-loading"></i> {{ __('view_pickups') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Statistics Grid --}}
            <div class="col-xxl-9 col-xl-8 col-md-12">
                <div class="row">
                    {{-- Pending Deliveries --}}
                    <div class="col-xxl-4 col-xl-6 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-2">
                                <div class="analytics-icon">
                                    <i class="las la-box-open"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ $pending_deliveries }}</h4>
                                    <p>{{ __('pending_deliveries') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Completed Deliveries --}}
                    <div class="col-xxl-4 col-xl-6 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-1">
                                <div class="analytics-icon">
                                    <i class="las la-check-circle"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ $completed_deliveries }}</h4>
                                    <p>{{ __('completed_deliveries') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Today's Deliveries --}}
                    <div class="col-xxl-4 col-xl-6 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-5">
                                <div class="analytics-icon">
                                    <i class="las la-calendar-check"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ $today_deliveries }}</h4>
                                    <p>{{ __('delivered_today') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pending Pickups --}}
                    <div class="col-xxl-4 col-xl-6 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-3">
                                <div class="analytics-icon">
                                    <i class="las la-truck-pickup"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ $pending_pickups }}</h4>
                                    <p>{{ __('pending_pickups') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Completed Pickups --}}
                    <div class="col-xxl-4 col-xl-6 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-4">
                                <div class="analytics-icon">
                                    <i class="las la-dolly"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ $completed_pickups }}</h4>
                                    <p>{{ __('completed_pickups') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Earnings --}}
                    <div class="col-xxl-4 col-xl-6 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-1">
                                <div class="analytics-icon">
                                    <i class="las la-wallet"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ setting('default_currency') }} {{ number_format($total_earnings, 2) }}</h4>
                                    <p>{{ __('total_earnings') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Assigned Deliveries List --}}
        <div class="row">
            <div class="col-12">
                <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">{{ __('recent_assigned_parcels') }}</h5>
                            <small class="text-muted">{{ __('latest_deliveries_assigned_to_you') }}</small>
                        </div>
                        <a href="{{ route('deliveryman.deliveries') }}" class="btn btn-sm sg-btn-primary">
                            {{ __('view_all') }} <i class="las la-arrow-right"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('parcel_no') }}</th>
                                    <th>{{ __('merchant') }}</th>
                                    <th>{{ __('customer') }}</th>
                                    <th>{{ __('phone') }}</th>
                                    <th>{{ __('address') }}</th>
                                    <th>{{ __('cod_amount') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th class="text-end">{{ __('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recent_deliveries as $parcel)
                                    <tr>
                                        <td>
                                            <a href="{{ route('deliveryman.parcel.detail', $parcel->id) }}" class="fw-bold text-primary">
                                                {{ $parcel->parcel_no }}
                                            </a>
                                        </td>
                                        <td>
                                            <span>{{ $parcel->merchant->company_name ?? ($parcel->merchant->user->first_name ?? 'N/A') }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $parcel->customer_name }}</span>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $parcel->customer_phone_number }}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1">
                                                <i class="las la-phone"></i> {{ $parcel->customer_phone_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="text-truncate d-inline-block" style="max-width: 160px;" title="{{ $parcel->customer_address }}">
                                                    {{ $parcel->customer_address }}
                                                </span>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($parcel->customer_address) }}" 
                                                   target="_blank" 
                                                   class="text-danger" 
                                                   title="{{ __('open_in_google_maps') }}">
                                                    <i class="las la-map-marked-alt font-18"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ setting('default_currency') }} {{ number_format($parcel->price, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($parcel->status == 'delivered')
                                                <span class="badge bg-success text-white">{{ __('delivered') }}</span>
                                            @elseif($parcel->status == 'delivery_assigned' || $parcel->status == 'processing')
                                                <span class="badge bg-warning text-dark">{{ __('processing') }}</span>
                                            @elseif($parcel->status == 'cancel' || $parcel->status == 'delivery_cancelled')
                                                <span class="badge bg-danger text-white">{{ __('cancelled') }}</span>
                                            @else
                                                <span class="badge bg-secondary text-white">{{ str_replace('_', ' ', $parcel->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('deliveryman.parcel.detail', $parcel->id) }}" class="btn btn-sm sg-btn-outline-primary">
                                                <i class="las la-eye"></i> {{ __('view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="las la-box-open font-36 d-block mb-2 text-muted"></i>
                                            {{ __('no_assigned_parcels_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
