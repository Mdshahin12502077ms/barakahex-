@extends('backend.layouts.master')

@section('title')
    {{ __('rider_report') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex flex-wrap justify-content-between align-items-center mb-12 gap-2">
                    <h3 class="section-title">{{ __('rider_report') }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.rider.report.csv', request()->all()) }}" class="btn btn-sm btn-success d-flex align-items-center gap-1 shadow-sm px-3">
                            <i class="las la-file-csv font-18"></i> <span>{{ __('export_to_csv') }}</span>
                        </a>
                        <button type="button" class="btn btn-sm btn-info d-flex align-items-center gap-1 text-white shadow-sm px-3" onclick="window.print();">
                            <i class="las la-print font-18"></i> <span>{{ __('print') }} / {{ __('pdf') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Top Filter Bar -->
                <div class="bg-white redious-border p-20 p-sm-30 mb-4 shadow-sm">
                    <form method="GET" action="{{ route('admin.rider.report') }}" id="riderFilterForm">
                        <div class="row g-3 align-items-end">
                            <!-- Date Range -->
                            <div class="col-12 col-md-3">
                                <label class="form-label font-13 fw-semibold">{{ __('from_date') }}</label>
                                <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label font-13 fw-semibold">{{ __('to_date') }}</label>
                                <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                            </div>

                            <!-- Hub/Branch Filter -->
                            <div class="col-12 col-md-3">
                                <label class="form-label font-13 fw-semibold">{{ __('branch') }} / {{ __('hub') }}</label>
                                <select class="form-select form-control select2" name="branch">
                                    <option value="all">{{ __('all_branches') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rider Search -->
                            <div class="col-12 col-md-3">
                                <label class="form-label font-13 fw-semibold">{{ __('rider') }}</label>
                                <select class="form-select form-control select2" name="rider_id">
                                    <option value="">{{ __('all_riders') }}</option>
                                    @foreach ($delivery_men as $dm)
                                        <option value="{{ $dm->id }}" {{ request('rider_id') == $dm->id ? 'selected' : '' }}>
                                            {{ $dm->user ? $dm->user->first_name . ' ' . $dm->user->last_name : 'ID #' . $dm->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Submit & Reset -->
                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('admin.rider.report') }}" class="btn btn-light border px-4">
                                    <i class="las la-sync"></i> {{ __('reset') }}
                                </a>
                                <button type="submit" class="btn sg-btn-primary px-4">
                                    <i class="las la-filter"></i> {{ __('filter') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 4 Top KPI Stat Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Assigned -->
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase font-12 fw-bold">{{ __('total_assigned') }}</span>
                                    <h3 class="mb-0 mt-2 fw-bold text-primary">{{ number_format($stats['total_assigned']) }}</h3>
                                    <small class="text-muted font-11">{{ __('parcels') }}</small>
                                </div>
                                <div class="rounded-circle p-3 bg-primary-light text-primary">
                                    <i class="las la-box font-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivered -->
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase font-12 fw-bold">{{ __('delivered') }}</span>
                                    <h3 class="mb-0 mt-2 fw-bold text-success">{{ number_format($stats['total_delivered']) }}</h3>
                                    <small class="text-success font-11 fw-semibold"><i class="las la-check"></i> {{ __('success') }}</small>
                                </div>
                                <div class="rounded-circle p-3 bg-success-light text-success">
                                    <i class="las la-truck font-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total COD Collected -->
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase font-12 fw-bold">{{ __('total_cod_collected') }}</span>
                                    <h3 class="mb-0 mt-2 fw-bold text-dark">&#2547; {{ number_format($stats['total_cod'], 2) }}</h3>
                                    <small class="text-muted font-11">{{ __('cash_collection') }}</small>
                                </div>
                                <div class="rounded-circle p-3 bg-info-light text-info">
                                    <i class="las la-money-bill-wave font-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Rate -->
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase font-12 fw-bold">{{ __('average_success_rate') }}</span>
                                    <h3 class="mb-0 mt-2 fw-bold {{ $stats['success_rate'] >= 80 ? 'text-success' : ($stats['success_rate'] >= 50 ? 'text-warning' : 'text-danger') }}">
                                        {{ $stats['success_rate'] }}%
                                    </h3>
                                    <small class="text-muted font-11">{{ __('overall_efficiency') }}</small>
                                </div>
                                <div class="rounded-circle p-3 {{ $stats['success_rate'] >= 80 ? 'bg-success-light text-success' : 'bg-warning-light text-warning' }}">
                                    <i class="las la-chart-pie font-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTable Card -->
                <div class="bg-white redious-border p-20 p-sm-30 shadow-sm">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <h5 class="fw-bold mb-0 text-dark">{{ __('rider_performance_details') }}</h5>
                        <div class="d-flex align-items-center gap-2" id="data_table_option_container">
                            <!-- DataTables Length & Search will attach here automatically -->
                        </div>
                    </div>
                    <div class="default-responsive-table">
                        {!! $dataTable->table(['class' => 'table table-hover align-middle mb-0 w-100']) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
