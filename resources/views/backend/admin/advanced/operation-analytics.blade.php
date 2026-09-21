@extends('backend.layouts.master')
@section('title', __('operations_analytics'))

@section('mainContent')
<section class="oftions">
    <div class="container-fluid">
        <!-- Header & Filter Form -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-5">
                <h3 class="section-title mb-0">{{ __('operations_analytics') }}</h3>
            </div>
            <div class="col-md-7
             text-end">
                <form action="{{ route('admin.analytics.operations') }}" method="GET" class="d-flex justify-content-end align-items-center gap-2">
                    <input type="date" name="from_date" class="form-control w-auto" value="{{ request('from_date') }}">
                    <span>{{ __('to') }}</span>
                    <input type="date" name="to_date" class="form-control w-auto" value="{{ request('to_date') }}">
                    <button type="submit" class="btn btn-primary"><i class="las la-search"></i> {{ __('filter') }}</button>
                    <a href="{{ route('admin.analytics.operations') }}" class="btn btn-secondary"><i class="las la-sync"></i></a>
                </form>
            </div>
        </div>

        <!-- KPI Cards (Native Theme Style) -->
        <div class="row">
            <!-- Total Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('total_parcels') }}</h6>
                        <h4>{{ number_format($data['total_parcel']) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Delivered Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('delivered') }}</h6>
                        <h4>{{ number_format($data['delivered_parcel']) }}</h4>
                        <small class="text-success font-weight-bold">{{ $data['delivery_success_rate'] }}% {{ __('success_rate') }}</small>
                    </div>
                </div>
            </div>

            <!-- Returned Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-warning redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('returned') }}</h6>
                        <h4>{{ number_format($data['returned_parcel']) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Failed Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-danger redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('failed') }} / {{ __('cancel') }}</h6>
                        <h4>{{ number_format($data['failed_parcel']) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Pending Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-info redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('pending') }}</h6>
                        <h4>{{ number_format($data['pending_parcel']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hub Performance Chart Section -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-4">
                        <h4>{{ __('hub_branch_performance') }} - {{ __('chart') }}</h4>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="branchPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hub Performance Section (Native Theme Style) -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-4 d-flex justify-content-between align-items-center">
                        <h4>{{ __('hub_branch_performance') }}</h4>
                        <div class="search-box">
                            <input type="text" id="branchSearch" class="form-control" placeholder="Search by branch name...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-start">{{ __('hub_name') }}</th>
                                    <th class="text-center">{{ __('total_processed') }}</th>
                                    <th class="text-center">{{ __('delivered') }}</th>
                                    <th class="text-center">{{ __('cancel') }}</th>
                                    <th class="text-center" style="min-width: 150px;">{{ __('success_rate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                  @foreach($data['hub_performance'] as $hub)
                                      @php
                                          $successRate = $hub->total > 0 ? round(($hub->delivered / $hub->total) * 100, 1) : 0;
                                          $cancelRate = $hub->total > 0 ? round(($hub->cancel / $hub->total) * 100, 1) : 0;
                                          $branchName = $hub->branch ? $hub->branch->name : __('unknown_branch');
                                      @endphp
                                      <tr class="branch-row" data-branch="{{ $branchName }}">
                                          <td class="text-start">
                                              <strong>{{ $branchName }}</strong>
                                        </td>
                                        <td class="text-center"><span class="badge badge-primary px-2">{{ number_format($hub->total) }}</span></td>
                                        <td class="text-center text-success"><strong>{{ number_format($hub->delivered) }}</strong></td>
                                        <td class="text-center text-danger"><strong>{{ number_format($hub->cancel) }}</strong></td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <span>{{ $successRate }}%</span>
                                                <div class="progress progress-sm" style="width: 80px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $successRate }}%" aria-valuenow="{{ $successRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(count($data['hub_performance']) == 0)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('no_data_found') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $chartLabels = [];
    $chartSuccessRates = [];
    $chartCancelRates = [];
    $chartTotalParcels = [];
    
    foreach($data['hub_performance'] as $hub) {
        $name = $hub->branch ? $hub->branch->name : __('unknown_branch');
        $successRate = $hub->total > 0 ? round(($hub->delivered / $hub->total) * 100, 1) : 0;
        $cancelRate = $hub->total > 0 ? round(($hub->cancel / $hub->total) * 100, 1) : 0;
        
        $chartLabels[] = $name;
        $chartSuccessRates[] = $successRate;
        $chartCancelRates[] = $cancelRate;
        $chartTotalParcels[] = $hub->total;
    }
@endphp

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('branchPerformanceChart').getContext('2d');
        var branchPerformanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: '{{ __("success_rate") }} (%)',
                        data: {!! json_encode($chartSuccessRates) !!},
                        backgroundColor: 'rgba(28, 200, 138, 0.8)', // Success Green
                        borderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: '{{ __("total_processed") }}',
                        data: {!! json_encode($chartTotalParcels) !!},
                        backgroundColor: 'rgba(78, 115, 223, 0.8)', // Primary Blue
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    },
                     {
                        label: '{{ __("cancel_rate") }}',
                        data: {!! json_encode($chartCancelRates) !!},
                        backgroundColor: 'rgba(231, 74, 59, 0.8)', 
                        borderColor: 'rgba(231, 74, 59, 1)',
                        borderWidth: 1,
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                responsive: false, scrollX: false,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: '{{ __("success_rate") }} (%)'
                        }
                    },
                    
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: '{{ __("total_processed") }}'
                        }
                    },

                     y2: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: '{{ __("cancel_rate") }} (%)'
                        }
                    },
                }
            }
        });
            
        // Branch Name Search
        const branchSearchInput = document.getElementById('branchSearch');
        if(branchSearchInput) {
            branchSearchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('.branch-row');
                
                rows.forEach(row => {
                    const branchName = row.getAttribute('data-branch').toLowerCase();
                    if (branchName.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
