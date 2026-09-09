@extends('backend.layouts.master')

@section('title')
    {{ __('Branch and Hub Report') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-20 gap-3">
        <div>
            <h3 class="section-title fw-bold text-dark mb-0">{{ __('Branch and Hub Report') }}</h3>
        </div>
    </div>

    {{-- Filter & Actions Bar --}}
    <div class="bg-white rounded-3 p-3 p-sm-4 mb-4 shadow-sm border border-light">
        <form method="GET" action="{{ route('admin.branch_hub.report') }}" id="hubReportFilterForm">
            <div class="row g-3 align-items-end">
                {{-- Date Range & Branch Filters --}}
                <div class="col-xl-8 col-lg-8 col-md-12">
                    <div class="row g-2 align-items-center">
                        {{-- From Date --}}
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label font-12 fw-semibold text-muted mb-1">{{ __('From Date') }}</label>
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
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label font-12 fw-semibold text-muted mb-1">{{ __('To Date') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">
                                    <i class="las la-calendar font-16"></i>
                                </span>
                                <input type="date" class="form-control form-control-sm border-start-0 ps-0" 
                                       name="to_date" id="to_date" 
                                       value="{{ request('to_date') }}">
                            </div>
                        </div>

                        {{-- Branch / Hub Select --}}
                        <div class="col-md-3 col-sm-12">
                            <label class="form-label font-12 fw-semibold text-muted mb-1">{{ __('Branch/Hub') }}</label>
                            <select class="form-select form-select-sm form-control" name="branch_id" id="branch_id" onchange="document.getElementById('hubReportFilterForm').submit();">
                                <option value="">{{ __('Search/Hub...') }}</option>
                                @if(isset($branches) && count($branches) > 0)
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (Export CSV & Print) --}}
                <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end mt-3 mt-lg-0">
                    <div class="d-inline-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-2">
                            <i class="las la-filter font-16"></i>
                            <span class="fw-semibold font-13">{{ __('Filter') }}</span>
                        </button>

                        <a href="{{ route('admin.hub.report.csv', request()->all()) }}" 
                           class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 bg-white px-3 py-2 rounded-2 border">
                            <i class="las la-download font-16 text-dark"></i>
                            <span class="fw-semibold font-13">{{ __('Export CSV') }}</span>
                        </a>

                        <button type="button" 
                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 bg-white px-3 py-2 rounded-2 border" 
                                onclick="window.print();">
                            <i class="las la-print font-16 text-dark"></i>
                            <span class="fw-semibold font-13">{{ __('Print') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- 5 Summary Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- 1. Total Received (Blue) --}}
        <div class="col-xxl col-xl col-md-4 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-inbox"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('Total Received') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-24 lh-1 mt-1">{{ number_format($summary['total_received'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Total Dispatched (Purple/Indigo) --}}
        <div class="col-xxl col-xl col-md-4 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #5b50e6 0%, #4338ca 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-shipping-fast"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('Total Dispatched') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-24 lh-1 mt-1">{{ number_format($summary['total_dispatched'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. In-Branch Pending (Amber/Yellow) --}}
        <div class="col-xxl col-xl col-md-4 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #e69d31 0%, #d97706 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-clock"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('In-Branch Pending') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-24 lh-1 mt-1">{{ number_format($summary['in_branch_pending'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Total Delivered (Emerald Green) --}}
        <div class="col-xxl col-xl col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-award"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('Total Delivered') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-24 lh-1 mt-1">{{ number_format($summary['total_delivered'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Total Return (Rose/Red) --}}
        <div class="col-xxl col-xl col-md-6 col-sm-6 col-12">
            <div class="card border-0 rounded-3 text-white p-3 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #d85263 0%, #be123c 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box">
                        <i class="las la-reply"></i>
                    </div>
                    <div>
                        <div class="text-white-50 font-12 fw-medium text-nowrap">{{ __('Total Return') }}</div>
                        <h3 class="fw-bold text-white mb-0 font-24 lh-1 mt-1">{{ number_format($summary['total_return'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report DataTable Card --}}
    <div class="bg-white rounded-3 p-3 p-sm-4 shadow-sm border border-light">
        <div class="default-responsive-table">
            {{ $dataTable->table(['class' => 'table table-hover align-middle mb-0 w-100 hub-report-table'], true) }}
        </div>
    </div>
</div>

{{-- Inline Scoped Styles to Match Mockup UI --}}
<style>
    .metric-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .hub-report-table th {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #64748b;
        font-size: 13px;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .hub-report-table td {
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 14px;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .hub-report-table tbody tr:hover {
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
