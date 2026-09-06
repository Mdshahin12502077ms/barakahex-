@extends('backend.layouts.master')

@section('title')
    {{ __('cod_and_earnings') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
        <div>
            <h4 class="section-title mb-1">{{ __('cod_and_earnings') }}</h4>
            <p class="text-muted small mb-0">{{ __('track_your_cash_in_hand_office_deposits_and_earnings') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveryman.dashboard') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-arrow-left"></i> {{ __('back_to_dashboard') }}
            </a>
        </div>
    </div>

    {{-- Financial Overview Counters --}}
    <div class="row">
        {{-- Cash in Hand --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-2">
                    <div class="analytics-icon">
                        <i class="las la-hand-holding-usd"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ setting('default_currency') }} {{ number_format($summary['cash_in_hand'] ?? 0, 2) }}</h4>
                        <p>{{ __('cash_in_hand') }} (COD)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Cash Collected --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-5">
                    <div class="analytics-icon">
                        <i class="las la-money-bill-wave"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ setting('default_currency') }} {{ number_format($summary['total_collected'] ?? 0, 2) }}</h4>
                        <p>{{ __('total_cash_collected') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deposited to Office --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-1">
                    <div class="analytics-icon">
                        <i class="las la-university"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ setting('default_currency') }} {{ number_format($summary['total_deposited'] ?? 0, 2) }}</h4>
                        <p>{{ __('deposited_to_office') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Earnings --}}
        <div class="col-xxl-3 col-xl-3 col-md-3">
            <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                <div class="analytics clr-3">
                    <div class="analytics-icon">
                        <i class="las la-coins"></i>
                    </div>
                    <div class="analytics-content no-line-braek">
                        <h4>{{ setting('default_currency') }} {{ number_format($summary['total_earnings'] ?? 0, 2) }}</h4>
                        <p>{{ __('total_earnings') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statement History Table Card --}}
    <div class="bg-white redious-border p-20 p-sm-30 mb-4">
        <form action="{{ route('deliveryman.accounts') }}" method="GET" class="mb-20">
            <div class="row g-3 align-items-center">
                {{-- Date Filters --}}
                <div class="col-lg-3 col-md-4">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="{{ __('from_date') }}">
                </div>
                <div class="col-lg-3 col-md-4">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="{{ __('to_date') }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <button class="btn sg-btn-primary w-100" type="submit">
                        <i class="las la-filter"></i> {{ __('filter') }}
                    </button>
                </div>
                {{-- Quick Tabs --}}
                <div class="col-lg-4 col-md-12">
                    <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
                        <a href="{{ route('deliveryman.accounts') }}" 
                           class="btn btn-sm {{ !request('source') || request('source') == 'all' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('all') }}
                        </a>
                        <a href="{{ route('deliveryman.accounts', ['source' => 'collection']) }}" 
                           class="btn btn-sm {{ request('source') == 'collection' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('collection') }}
                        </a>
                        <a href="{{ route('deliveryman.accounts', ['source' => 'deposit']) }}" 
                           class="btn btn-sm {{ request('source') == 'deposit' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('deposit') }}
                        </a>
                        <a href="{{ route('deliveryman.accounts', ['source' => 'earning']) }}" 
                           class="btn btn-sm {{ request('source') == 'earning' ? 'sg-btn-primary' : 'sg-btn-outline-primary' }}">
                            {{ __('earnings') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Account Statements Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('date') }}</th>
                        <th>{{ __('source') }}</th>
                        <th>{{ __('parcel_no') }}</th>
                        <th>{{ __('type') }}</th>
                        <th>{{ __('amount') }}</th>
                        <th class="text-end">{{ __('details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statements as $statement)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ date('d M Y', strtotime($statement->date ?: $statement->created_at)) }}</div>
                                <small class="text-muted">{{ date('h:i A', strtotime($statement->created_at)) }}</small>
                            </td>
                            <td>
                                @if($statement->source == 'cash_collection')
                                    <span class="badge bg-success text-white">
                                        <i class="las la-arrow-down"></i> {{ __('cash_collection') }}
                                    </span>
                                @elseif($statement->source == 'cash_given_to_staff')
                                    <span class="badge bg-info text-white">
                                        <i class="las la-arrow-up"></i> {{ __('cash_given_to_staff') }}
                                    </span>
                                @elseif(in_array($statement->source, ['pickup_commission', 'parcel_delivery', 'delivery_commission', 'commission']))
                                    <span class="badge bg-primary text-white">
                                        <i class="las la-award"></i> {{ __('commission') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white">{{ __(str_replace('_', ' ', $statement->source)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($statement->parcel)
                                    <a href="{{ route('deliveryman.parcel.detail', $statement->parcel->id) }}" class="fw-bold text-primary">
                                        {{ $statement->parcel->parcel_no }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($statement->type == 'income')
                                    <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1">
                                        <i class="las la-plus"></i> {{ __('income') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger fw-bold px-2 py-1">
                                        <i class="las la-minus"></i> {{ __('expense') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold {{ $statement->type == 'income' ? 'text-success' : 'text-danger' }} font-16">
                                    {{ $statement->type == 'income' ? '+' : '-' }} {{ setting('default_currency') }} {{ number_format($statement->amount, 2) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted small">{{ $statement->details ? __($statement->details) : 'N/A' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="las la-file-invoice-dollar font-40 d-block mb-2 text-muted"></i>
                                <h5>{{ __('no_statements_found') }}</h5>
                                <p class="small mb-0">{{ __('you_do_not_have_any_account_statements_yet') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($statements->hasPages())
            <div class="mt-20 d-flex justify-content-end">
                {!! $statements->withQueryString()->links() !!}
            </div>
        @endif
    </div>
</div>
@endsection
