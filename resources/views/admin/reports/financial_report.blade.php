@extends('backend.layouts.master')

@section('title')
    {{ __('financial_report') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-20 gap-3">
        <div>
            <h3 class="section-title fw-bold text-dark mb-0">{{ __('financial_report') }}</h3>
        </div>
    </div>

    {{-- Filter & Actions Bar --}}
    <div class="bg-white rounded-3 p-3 p-sm-4 mb-4 shadow-sm border border-light">
        <form method="GET" action="{{ route('admin.financial.report') }}" id="financialReportFilterForm">
            <div class="row g-3 align-items-end justify-content-between">
                {{-- Date Range Filters --}}
                <div class="col-xl-6 col-lg-7 col-md-12">
                    <div class="row g-2 align-items-center">
                        {{-- From Date --}}
                        <div class="col-md-5 col-sm-6">
                            <label class="form-label font-12 fw-semibold text-muted mb-1">{{ __('from_date') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">
                                    <i class="las la-calendar font-16"></i>
                                </span>
                                <input type="date" class="form-control form-control-sm border-start-0 ps-0" 
                                       name="from_date" id="from_date" 
                                       value="{{ request('from_date') }}">
                            </div>
                        </div>

                        {{-- Separator --}}
                        <div class="col-auto d-none d-md-block pt-3 text-muted">
                            <span class="fw-bold">-</span>
                        </div>

                        {{-- To Date --}}
                        <div class="col-md-5 col-sm-6">
                            <label class="form-label font-12 fw-semibold text-muted mb-1">{{ __('to_date') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">
                                    <i class="las la-calendar font-16"></i>
                                </span>
                                <input type="date" class="form-control form-control-sm border-start-0 ps-0" 
                                       name="to_date" id="to_date" 
                                       value="{{ request('to_date') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (Filter, Export CSV & Print) --}}
                <div class="col-xl-6 col-lg-5 col-md-12 text-lg-end mt-3 mt-lg-0">
                    <div class="d-inline-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-2">
                            <i class="las la-filter font-16"></i>
                            <span class="fw-semibold font-13">{{ __('filter') }}</span>
                        </button>

                        <a href="{{ route('admin.financial.report.csv', request()->all()) }}" 
                           class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 bg-white px-3 py-2 rounded-2 border">
                            <i class="las la-download font-16 text-dark"></i>
                            <span class="fw-semibold font-13">{{ __('export_to_csv') }}</span>
                        </a>

                        <button type="button" 
                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 bg-white px-3 py-2 rounded-2 border" 
                                onclick="window.print();">
                            <i class="las la-print font-16 text-dark"></i>
                            <span class="fw-semibold font-13">{{ __('print') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- 8 Financial KPI Summary Cards Grid (Matching Mockup) --}}
    <div class="row g-3 mb-4">
        {{-- 1. Revenue (Deep Teal/Cyan) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-chart-line"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('revenue') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['revenue'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Total COD (Royal Blue) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('total_cod') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['total_cod'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Delivery Charge (Indigo Violet) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-truck"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('delivery_charge') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['delivery_charge'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Return Charge (Amber Orange) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-undo-alt"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('return_charge') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['return_charge'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Rider Commission (Crimson Magenta) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #be185d 0%, #881337 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-motorcycle"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('rider_commission') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['rider_commission'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Merchant Payable (Navy Slate) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #334155 0%, #1e293b 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-wallet"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('merchant_payable') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['merchant_payable'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 7. Expense (Coral Red) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('expense') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['expense'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 8. Net Profit (Emerald Green) --}}
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-chart-bar"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('net_profit') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-22 lh-1 mt-1">{{ format_price($summary['net_profit'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report DataTable Card --}}
    <div class="bg-white rounded-3 p-3 p-sm-4 shadow-sm border border-light">
        <div class="default-responsive-table">
            {!! $dataTable->table(['class' => 'table table-hover align-middle mb-0 w-100 financial-report-table'], true) !!}
        </div>
    </div>
</div>

{{-- Scoped Styles Matching Approved Mockup --}}
<style>
    .metric-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .financial-report-table th {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #64748b;
        font-size: 13px;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .financial-report-table td {
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 14px;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .financial-report-table tbody tr:hover {
        background-color: #f8fafc;
    }

    @media print {
        .header-top,
        .btn,
        form,
        .navbar-dark-v1,
        header {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card,
        .table-responsive {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
