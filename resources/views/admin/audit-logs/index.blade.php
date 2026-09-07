@extends('backend.layouts.master')

@section('title')
    {{ __('audit_logs') }} {{ __('lists') }}
@endsection

@push('css')
<style>
    .audit-stat-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        padding: 14px 16px;
        transition: all 0.25s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .audit-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.06);
    }
    .audit-stat-card.active {
        border-color: #0d6efd;
        background-color: #f8fbff;
        box-shadow: 0 0 0 2px rgba(13,110,253,0.2);
    }
    .audit-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .filter-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .audit-filter-pill {
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .audit-filter-pill.active {
        font-weight: 600;
    }
    #audit-logs-table tfoot,
    .yajra-dataTable tfoot,
    table tfoot {
        display: none !important;
        border: none !important;
        visibility: hidden !important;
        height: 0 !important;
        padding: 0 !important;
    }
</style>
@endpush

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h3 class="section-title mb-1">
                                <i class="las la-history text-primary"></i> {{ __('audit_logs') }}
                            </h3>
                            <p class="text-muted font-13 mb-0">
                                {{ __('Monitor and track every status change, COD update, rider assignment, and settlement.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Stats Cards -->
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="audit-stat-card active audit-filter-card" data-action="">
                        <div>
                            <div class="text-muted font-12 fw-medium">{{ __('All Logs') }}</div>
                            <div class="fs-5 fw-bold text-dark mt-1">{{ number_format($stats['total'] ?? 0) }}</div>
                        </div>
                        <div class="audit-stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="las la-list"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="audit-stat-card audit-filter-card" data-action="status_change">
                        <div>
                            <div class="text-muted font-12 fw-medium">{{ __('Status') }}</div>
                            <div class="fs-5 fw-bold text-primary mt-1">{{ number_format($stats['status_change'] ?? 0) }}</div>
                        </div>
                        <div class="audit-stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="las la-sync"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="audit-stat-card audit-filter-card" data-action="cod_change">
                        <div>
                            <div class="text-muted font-12 fw-medium">{{ __('COD') }}</div>
                            <div class="fs-5 fw-bold text-warning mt-1">{{ number_format($stats['cod_change'] ?? 0) }}</div>
                        </div>
                        <div class="audit-stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="las la-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="audit-stat-card audit-filter-card" data-action="settlement">
                        <div>
                            <div class="text-muted font-12 fw-medium">{{ __('Settlement') }}</div>
                            <div class="fs-5 fw-bold text-success mt-1">{{ number_format($stats['settlement'] ?? 0) }}</div>
                        </div>
                        <div class="audit-stat-icon bg-success bg-opacity-10 text-success">
                            <i class="las la-wallet"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="audit-stat-card audit-filter-card" data-action="rider_assign">
                        <div>
                            <div class="text-muted font-12 fw-medium">{{ __('Rider') }}</div>
                            <div class="fs-5 fw-bold text-purple mt-1" style="color:#6f42c1;">{{ number_format($stats['rider_assign'] ?? 0) }}</div>
                        </div>
                        <div class="audit-stat-icon text-white" style="background-color: #6f42c1;">
                            <i class="las la-motorcycle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="audit-stat-card audit-filter-card" data-action="return">
                        <div>
                            <div class="text-muted font-12 fw-medium">{{ __('Return') }}</div>
                            <div class="fs-5 fw-bold text-danger mt-1">{{ number_format($stats['return'] ?? 0) }}</div>
                        </div>
                        <div class="audit-stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="las la-undo"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="filter-card p-3 mb-3">
                <div class="row align-items-end g-3">
                    <!-- From Date -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="from_date" class="form-label font-12 fw-bold text-secondary mb-1">
                            <i class="las la-calendar text-primary"></i> {{ __('From Date') }}
                        </label>
                        <input type="date" id="from_date" name="from_date" class="form-control form-control-sm" style="border-radius: 6px;">
                    </div>

                    <!-- To Date -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="to_date" class="form-label font-12 fw-bold text-secondary mb-1">
                            <i class="las la-calendar text-primary"></i> {{ __('To Date') }}
                        </label>
                        <input type="date" id="to_date" name="to_date" class="form-control form-control-sm" style="border-radius: 6px;">
                    </div>

                    <!-- Action Selector -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="action_select" class="form-label font-12 fw-bold text-secondary mb-1">
                            <i class="las la-tag text-primary"></i> {{ __('Action Type') }}
                        </label>
                        <select id="action_select" class="form-select form-select-sm" style="border-radius: 6px;">
                            <option value="">{{ __('All Actions') }}</option>
                            <option value="status_change">{{ __('Status Change') }}</option>
                            <option value="cod_change">{{ __('COD Change') }}</option>
                            <option value="settlement">{{ __('Settlement') }}</option>
                            <option value="charge_change">{{ __('Charge Change') }}</option>
                            <option value="rider_assign">{{ __('Rider Assignment') }}</option>
                            <option value="return">{{ __('Return') }}</option>
                        </select>
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                        <button type="button" id="btn_apply_filter" class="btn btn-primary btn-sm flex-fill" style="border-radius: 6px;">
                            <i class="las la-filter"></i> {{ __('Filter') }}
                        </button>
                        <button type="button" id="btn_reset_filter" class="btn btn-outline-secondary btn-sm flex-fill" style="border-radius: 6px;">
                            <i class="las la-redo"></i> {{ __('Reset') }}
                        </button>
                    </div>
                </div>

                <!-- Quick Action Pills -->
                <hr class="my-2 text-muted opacity-25">
                <div class="d-flex flex-wrap gap-2 align-items-center pt-1">
                    <span class="font-12 text-muted fw-bold"><i class="las la-bolt text-warning"></i> {{ __('Quick Filter') }}:</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm audit-filter-pill active" data-action="">
                        {{ __('All') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm audit-filter-pill" data-action="status_change">
                        <i class="las la-sync"></i> {{ __('Status Change') }}
                    </button>
                    <button type="button" class="btn btn-outline-warning text-dark btn-sm audit-filter-pill" data-action="cod_change">
                        <i class="las la-money-bill-wave"></i> {{ __('COD Change') }}
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm audit-filter-pill" data-action="settlement">
                        <i class="las la-wallet"></i> {{ __('Settlement') }}
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm audit-filter-pill" data-action="charge_change">
                        <i class="las la-calculator"></i> {{ __('Charge Change') }}
                    </button>
                    <button type="button" class="btn btn-outline-purple btn-sm audit-filter-pill" style="border-color:#6f42c1; color:#6f42c1;" data-action="rider_assign">
                        <i class="las la-motorcycle"></i> {{ __('Rider Assignment') }}
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm audit-filter-pill" data-action="return">
                        <i class="las la-undo"></i> {{ __('Return') }}
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30 shadow-sm" style="border-radius: 10px;">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="default-list-table table-responsive yajra-dataTable">
                            {{ $dataTable->table(['class' => 'table table-bordered table-hover align-middle mb-0 w-100'], false) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var selectedAction = '';

            function reloadAuditTable() {
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var action = $('#action_select').val();

                var url = '{{ route("audit.logs") }}' + '?';
                var params = [];

                if (action) {
                    params.push('action_type=' + encodeURIComponent(action));
                }
                if (fromDate) {
                    params.push('from_date=' + encodeURIComponent(fromDate));
                }
                if (toDate) {
                    params.push('to_date=' + encodeURIComponent(toDate));
                }

                url += params.join('&');

                var table = window.LaravelDataTables['audit-logs-table'];
                if (table) {
                    table.ajax.url(url).load();
                }
            }

            // Apply filter button
            $('#btn_apply_filter').on('click', function() {
                reloadAuditTable();
            });

            // Date inputs trigger auto reload on change
            $('#from_date, #to_date').on('change', function() {
                reloadAuditTable();
            });

            // Dropdown change
            $('#action_select').on('change', function() {
                var action = $(this).val();
                syncActiveButtons(action);
                reloadAuditTable();
            });

            // Quick Filter Pills Click
            $('.audit-filter-pill').on('click', function() {
                var action = $(this).data('action');
                $('#action_select').val(action);
                syncActiveButtons(action);
                reloadAuditTable();
            });

            // KPI Cards Click
            $('.audit-filter-card').on('click', function() {
                var action = $(this).data('action');
                $('#action_select').val(action);
                syncActiveButtons(action);
                reloadAuditTable();
            });

            // Sync visual active states
            function syncActiveButtons(action) {
                // Sync Pills
                $('.audit-filter-pill').removeClass('active btn-primary btn-warning btn-success btn-info btn-danger')
                    .addClass('btn-outline-secondary');
                $('.audit-filter-pill[data-action="' + action + '"]')
                    .removeClass('btn-outline-secondary')
                    .addClass('active btn-primary');

                // Sync Cards
                $('.audit-stat-card').removeClass('active');
                $('.audit-stat-card[data-action="' + action + '"]').addClass('active');
            }

            // Reset Button
            $('#btn_reset_filter').on('click', function() {
                $('#from_date').val('');
                $('#to_date').val('');
                $('#action_select').val('');
                syncActiveButtons('');
                reloadAuditTable();
            });
        });
    </script>
@endpush
