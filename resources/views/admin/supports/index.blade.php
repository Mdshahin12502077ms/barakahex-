@extends('backend.layouts.master')
@section('support', 'active')
@section('title')
{{ __('Support Tickets') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    <div class="row gx-20">
        <div class="col-lg-12">

            {{-- Header --}}
            <div class="header-top d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="section-title">{{ __('Support Tickets') }}</h3>
                    <p class="mb-1">{{ __('Manage all merchant support tickets') }}</p>
                </div>
                <div class="oftions-content-right mb-12 filterOPT d-flex gap-2">
                    <a href="#" class="d-flex align-items-center btn btn-sm sg-btn-primary" id="filterBTN">
                        <i class="las la-filter" style="font-size: 12px;"></i>
                    </a>
                </div>
            </div>

            {{-- Filter Panel --}}
            <div class="row">
                <div class="col-lg-12" id="filterSection">
                    <div class="hidden-filter bg-white redious-border p-20 p-sm-30 mb-4">
                        <div class="row gx-6 gy-3">

                            {{-- Status Filter --}}
                            <div class="col-3">
                                <label class="form-label" for="filter_status">{{ __('Status') }}</label>
                                <select class="without_search form-select form-control form-select-sm filterable"
                                    id="filter_status" name="status">
                                    <option value="">{{ __('Any Status') }}</option>
                                    <option value="new">{{ __('New') }}</option>
                                    <option value="processing">{{ __('Processing') }}</option>
                                    <option value="resolved">{{ __('Resolved') }}</option>
                                    <option value="closed">{{ __('Closed') }}</option>
                                </select>
                            </div>

                            {{-- Priority Filter --}}
                            <div class="col-3">
                                <label class="form-label" for="filter_priority">{{ __('Priority') }}</label>
                                <select class="without_search form-select form-control form-select-sm filterable"
                                    id="filter_priority" name="priority">
                                    <option value="">{{ __('Any Priority') }}</option>
                                    <option value="low">{{ __('Low') }}</option>
                                    <option value="medium">{{ __('Medium') }}</option>
                                    <option value="high">{{ __('High') }}</option>
                                    <option value="critical">{{ __('Critical') }}</option>
                                </select>
                            </div>

                            {{-- Ticket Type Filter --}}
                            <div class="col-3">
                                <label class="form-label" for="filter_ticket_type">{{ __('Ticket Type') }}</label>
                                <select class="without_search form-select form-control form-select-sm filterable"
                                    id="filter_ticket_type" name="ticket_type">
                                    <option value="">{{ __('Any Type') }}</option>
                                    <option value="parcel_issue">{{ __('Parcel Issue') }}</option>
                                    <option value="payment_issue">{{ __('Payment Issue') }}</option>
                                    <option value="account_issue">{{ __('Account Issue') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                            </div>

                            {{-- Date Filter --}}
                            <div class="col-3">
                                <label class="form-label" for="filter_date">{{ __('Date Range') }}</label>
                                <input type="text" name="created_at" id="filter_date"
                                    class="form-control date-range filterable" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                            </div>

                            {{-- Filter Buttons --}}
                            <div class="col-12 text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <div class="mb-3">
                                        <button type="button" id="resetFilter" class="btn sg-btn-outline-primary">
                                            <i class="las la-redo-alt"></i> {{ __('Reset') }}
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" id="applyFilter" class="btn sg-btn-primary">
                                            <i class="las la-filter"></i> {{ __('Filter') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Badges --}}
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="las la-ticket-alt"></i> {{ __('New') }}:
                            <strong>{{ $counts['new'] ?? 0 }}</strong>
                        </span>
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="las la-spinner"></i> {{ __('Processing') }}:
                            <strong>{{ $counts['processing'] ?? 0 }}</strong>
                        </span>
                        <span class="badge bg-success px-3 py-2">
                            <i class="las la-check-circle"></i> {{ __('Resolved') }}:
                            <strong>{{ $counts['resolved'] ?? 0 }}</strong>
                        </span>
                        <span class="badge bg-dark px-3 py-2">
                            <i class="las la-times-circle"></i> {{ __('Closed') }}:
                            <strong>{{ $counts['closed'] ?? 0 }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- DataTable --}}
            <section class="oftions">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="default-list-table table-responsive yajra-dataTable">
                                            {{ $dataTable->table(['class' => 'dt-responsive table'], true) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection

@push('script')

<script>
    
        $('#filterBTN').on('click', function (e) {
            e.preventDefault();
            $('#filterSection').toggleClass('d-none');
        });

     
        $('#applyFilter').on('click', function () {
            var table = window.LaravelDataTables['dataTableBuilder'];
            table.draw();
        });

        // Reset Filter
        $('#resetFilter').on('click', function () {
            $('#filter_status, #filter_priority, #filter_ticket_type').val('');
            $('#filter_date').val('');
            var table = window.LaravelDataTables['dataTableBuilder'];
            table.draw();
        });

       
        // Status change AJAX (from status.blade.php select dropdown)
        $(document).on('change', '#status', function () {
            var ticketId = $(this).data('id');
            var status = $(this).val();

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $.ajax({
                url: '{{ route("admin.support.status") }}',
                type: 'POST',
                data: {
                    status: status,
                    ticketId: ticketId,
                 },
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message ?? 'Status updated!');
                    } else {
                        toastr.error(response.message ?? 'Something went wrong!');
                    }
                },
                error: function () {
                    toastr.error('Failed to update status.');
                }
            });
        });
</script>
@endpush