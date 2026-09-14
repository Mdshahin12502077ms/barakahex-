@extends('backend.layouts.master')
@section('title')
    {{ __('Support Tickets') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <!-- Top Statistics Cards -->
        <div class="row gx-20 mb-2">
            <div class="col-xxl-3 col-xl-3 col-md-6">
                <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                    <div class="analytics clr-1">
                        <div class="analytics-icon">
                            <i class="las la-ticket-alt"></i>
                        </div>
                        <div class="analytics-content no-line-braek">
                            <h4>{{ $total_ticket ?? 0 }}</h4>
                            <p>{{ __('Total Tickets') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-md-6">
                <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                    <div class="analytics clr-2">
                        <div class="analytics-icon">
                            <i class="las la-hourglass-half"></i>
                        </div>
                        <div class="analytics-content no-line-braek">
                            <h4>{{ $processing_ticket ?? 0 }}</h4>
                            <p>{{ __('Processing') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-md-6">
                <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                    <div class="analytics clr-3">
                        <div class="analytics-icon">
                            <i class="las la-check-circle"></i>
                        </div>
                        <div class="analytics-content no-line-braek">
                            <h4>{{ $resolved_ticket ?? 0 }}</h4>
                            <p>{{ __('Resolved') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-md-6">
                <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                    <div class="analytics clr-4">
                        <div class="analytics-icon">
                            <i class="las la-lock"></i>
                        </div>
                        <div class="analytics-content no-line-braek">
                            <h4>{{ $closed_ticket ?? 0 }}</h4>
                            <p>{{ __('Closed') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     



        <!-- Main DataTable Card -->
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title mb-0">{{ __('Support Tickets') }}</h3>
                    <div class="oftions-content-right">
                        {{-- @if(Route::has('merchant.support-tickets.create')) --}}
                            <a href="{{ route('merchant.support-tickets.create') }}" class="btn sg-btn-primary d-flex align-items-center gap-2">
                                <i class="las la-plus"></i>
                                <span>{{ __('Create New Ticket') }}</span>
                            </a>
                        {{-- @endif --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="bg-white redious-border p-20 p-sm-30">
                            <div class="default-list-table table-responsive yajra-dataTable">
                                {{ $dataTable->table(['class' => 'dt-responsive table w-100'], true) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
