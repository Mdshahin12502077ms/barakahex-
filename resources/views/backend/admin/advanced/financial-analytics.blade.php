@extends('backend.layouts.master')
@section('title', 'Financial Analytics')

@section('mainContent')
<section class="oftions">
    <div class="container-fluid">
        <!-- Header & Filter Form -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-5">
                <h3 class="section-title mb-0">Financial Analytics</h3>
            </div>
            <div class="col-md-7 text-end">
                <form action="{{ route('admin.analytics.financial') }}" method="GET" class="d-flex justify-content-end align-items-center gap-2">
                    <input type="date" name="start_date" class="form-control w-auto" value="{{ request('start_date') }}">
                    <span>{{ __('to') }}</span>
                    <input type="date" name="end_date" class="form-control w-auto" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-primary"><i class="las la-search"></i> {{ __('filter') }}</button>
                    <a href="{{ route('admin.analytics.financial') }}" class="btn btn-secondary"><i class="las la-sync"></i></a>
                </form>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row">
            <!-- Total Revenue -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Total Revenue</h6>
                        <h4>${{ number_format($data['total_revenue'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Delivery Charge -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Delivery Charge</h6>
                        <h4>${{ number_format($data['delivery_charge'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- COD Charge -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-info redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>COD Charge</h6>
                        <h4>${{ number_format($data['cod_charge'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Return Charge -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-warning redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Return Charge</h6>
                        <h4>${{ number_format($data['return_charge'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Rider Commission -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-danger redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Rider Commission</h6>
                        <h4>${{ number_format($data['rider_commission'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Merchant Payable -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-primary redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Merchant Payable</h6>
                        <h4>${{ number_format($data['merchant_payable'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Operating Expense -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-warning redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Operating Expense</h6>
                        <h4>${{ number_format($data['operating_expense'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                    <div class="statistics-info mb-3">
                        <h6>Net Profit</h6>
                        <h4>${{ number_format($data['net_profit'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-4">
                        <h4>Financial Overview Chart</h4>
                    </div>
                    <div style="height: 400px;">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        
        const data = {
            labels: ['Total Revenue', 'Operating Expense', 'Net Profit', 'Delivery Charge', 'COD Charge', 'Return Charge', 'Rider Commission', 'Merchant Payable'],
            datasets: [{
                label: 'Financial Data ($)',
                data: [
                    {{ $data['total_revenue'] ?? 0 }},
                    {{ $data['operating_expense'] ?? 0 }},
                    {{ $data['net_profit'] ?? 0 }},
                    {{ $data['total_delivery_charge'] ?? 0 }},
                    {{ $data['cod_charge'] ?? 0 }},
                    {{ $data['return_charge'] ?? 0 }},
                    {{ $data['rider_commission'] ?? 0 }},
                    {{ $data['merchant_payable'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.6)', 
                    'rgba(255, 193, 7, 0.6)',  
                    'rgba(25, 135, 84, 0.6)',  
                    'rgba(25, 135, 84, 0.6)',  
                    'rgba(13, 202, 240, 0.6)', 
                    'rgba(255, 193, 7, 0.6)',  
                    'rgba(220, 53, 69, 0.6)',  
                    'rgba(13, 110, 253, 0.6)'  
                ],
                borderColor: [
                    'rgba(13, 110, 253, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(25, 135, 84, 1)',
                    'rgba(25, 135, 84, 1)',
                    'rgba(13, 202, 240, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(13, 110, 253, 1)'
                ],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: false, scrollX: false,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        new Chart(ctx, config);
    });
</script>
@endpush
@endsection
