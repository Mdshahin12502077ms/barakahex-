@extends('backend.layouts.master')
@section('title', __('rider_analytics'))

@section('mainContent')
<section class="oftions">
    <div class="container-fluid">
        <!-- Header & Filter Form -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="section-title mb-0">{{ __('rider_analytics') }}</h3>
            </div>
            <div class="col-md-6 text-end">
                <form action="" method="GET" class="d-flex justify-content-end align-items-center gap-2">
                    <input type="date" name="from_date" class="form-control w-auto" value="{{ request('from_date') }}">
                    <span>{{ __('to') }}</span>
                    <input type="date" name="to_date" class="form-control w-auto" value="{{ request('to_date') }}">
                    <button type="submit" class="btn btn-primary"><i class="las la-search"></i> {{ __('filter') }}</button>
                    <a href="{{ route('admin.analytics.rider') }}" class="btn btn-secondary"><i class="las la-sync"></i></a>
                </form>
            </div>
        </div>

        <!-- KPI Cards (8 Metrics - Static Design) -->
        <div class="row">
            <!-- Assigned Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('assigned_parcels') }}</h6>
                        <h4>{{ number_format($data['assigned_parcels'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Delivered Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('delivered_parcels') }}</h6>
                        <h4>{{ number_format($data['delivered_parcels'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Failed Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-danger redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('failed_parcels') }}</h6>
                        <h4>{{ number_format($data['failed_parcels'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Returned Parcels -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-warning redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('returned_parcels') }}</h6>
                        <h4>{{ number_format($data['returned_parcels'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('success_rate') }}</h6>
                        <h4>{{ number_format($data['success_rate'] ?? 0, 1) }}%</h4>
                    </div>
                </div>
            </div>

            <!-- Avg Delivery Time -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-info redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('avg_delivery_time') }}</h6>
                        <h4>{{ number_format($data['avg_delivery_time'] ?? 0, 1) }} <small>{{ __('mins') }}</small></h4>
                    </div>
                </div>
            </div>

            <!-- COD Collection -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('cod_collection') }}</h6>
                        <h4>{{ format_price($data['cod_collection'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Rider Commission -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>{{ __('rider_commission') }}</h6>
                        <h4>{{ format_price($data['rider_commission'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rider Analytics Chart -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-4">
                        <h4>{{ __('rider_performance_chart') }}</h4>
                    </div>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="riderPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rider Leaderboard Table -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-4 d-flex justify-content-between align-items-center">
                        <h4>{{ __('rider_leaderboard') }}</h4>
                        <div class="search-box">
                            <input type="text" id="riderSearch" class="form-control" placeholder="Search by phone...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-start">{{ __('rider_name') }}</th>
                                    <th class="text-center">{{ __('assigned') }}</th>
                                    <th class="text-center">{{ __('delivered') }}</th>
                                    <th class="text-center">{{ __('failed') }} / {{ __('returned') }}</th>
                                    <th class="text-center">{{ __('success_rate') }}</th>
                                    <th class="text-center">{{ __('cod_collected') }}</th>
                                    <th class="text-end">{{ __('commission') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['rider_performance'] ?? [] as $rider)
                                    <tr class="rider-row" data-phone="{{ $rider->phone ?? '' }}">
                                        <td class="text-start">
                                            <strong>{{ $rider->name ?? __('unknown') }}</strong><br>
                                            <small class="text-muted">{{ $rider->phone ?? '' }}</small>
                                        </td>
                                        <td class="text-center"><span class="badge badge-primary px-2">{{ number_format($rider->assigned ?? 0) }}</span></td>
                                        <td class="text-center text-success"><strong>{{ number_format($rider->delivered ?? 0) }}</strong></td>
                                        <td class="text-center text-danger"><strong>{{ number_format($rider->failed ?? 0) }} / {{ number_format($rider->returned ?? 0) }}</strong></td>
                                        <td class="text-center">{{ number_format($rider->success_rate ?? 0, 1) }}%</td>
                                        <td class="text-center">{{ format_price($rider->cod_collected ?? 0) }}</td>
                                        <td class="text-end text-success"><strong>{{ format_price($rider->commission ?? 0) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">{{ __('no_data_found') }}</td>
                                    </tr>
                                @endforelse
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
    $chartCodCollected = [];
    $chartCommission = [];
    
    $riders = $data['rider_performance'] ?? [];
    foreach($riders as $rider) {
        $chartLabels[] = $rider->name ?? __('unknown');
        $chartCodCollected[] = $rider->cod_collected ?? 0;
        $chartCommission[] = $rider->commission ?? 0;
    }
@endphp

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('riderPerformanceChart');
        if(ctx) {
            ctx = ctx.getContext('2d');
            var riderPerformanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: '{{ __("cod_collection") }}',
                            data: {!! json_encode($chartCodCollected) !!},
                            backgroundColor: 'rgba(78, 115, 223, 0.8)', 
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: '{{ __("rider_commission") }}',
                            data: {!! json_encode($chartCommission) !!},
                            backgroundColor: 'rgba(28, 200, 138, 0.8)', 
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __("cod_collection") }}'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: '{{ __("commission") }}'
                            }
                        }
                    }
                }
            });
        }

        // Rider Phone Search
        const searchInput = document.getElementById('riderSearch');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('.rider-row');
                
                rows.forEach(row => {
                    const phone = row.getAttribute('data-phone').toLowerCase();
                    if (phone.includes(filter)) {
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
