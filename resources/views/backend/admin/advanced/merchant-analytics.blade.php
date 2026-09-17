@extends('backend.layouts.master')
@section('title', __('merchant_analytics'))

@section('mainContent')
<section class="oftions">
    <div class="container-fluid">
        <!-- Header & Filter Form -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-5">
                <h3 class="section-title mb-0">Merchant Analytics</h3>
            </div>
            <div class="col-md-7 text-end">
                <form action="{{ route('admin.analytics.merchant') }}" method="GET" class="d-flex justify-content-end align-items-center gap-2">
                    <input type="date" id="start_date" name="start_date" class="form-control w-auto" value="{{ request('start_date') }}">
                    <span class="text-nowrap">{{ __('to') }}</span>
                    <input type="date" id="end_date" name="end_date" class="form-control w-auto" value="{{ request('end_date') }}">
                    <button type="submit" id="filter_btn" class="btn btn-primary text-nowrap"><i class="las la-sync"></i> Sync Data</button>
                    <a href="{{ route('admin.analytics.merchant') }}" class="btn btn-secondary"><i class="las la-redo"></i></a>
                </form>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row">
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Total Orders</h6>
                        <h4>{{ number_format($data['total_orders']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Delivered Orders</h6>
                        <h4>{{ number_format($data['delivered_orders']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-danger redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Failed Orders</h6>
                        <h4>{{ number_format($data['failed_orders']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-warning redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Returned Orders</h6>
                        <h4>{{ number_format($data['returned_orders']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-info redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Return Rate</h6>
                        <h4>{{ $data['return_rate'] }}%</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>COD Amount</h6>
                        <h4>${{ number_format($data['cod_amount'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Settlement Amount</h6>
                        <h4>${{ number_format($data['settlement_amount'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Merchant Revenue</h6>
                        <h4>${{ number_format($data['merchant_revenue'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-4">
                        <h4>Merchant Performance Overview</h4>
                    </div>
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-hover table-striped']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    {!! $dataTable->scripts() !!}
@endpush
