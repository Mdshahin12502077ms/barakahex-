@extends('backend.layouts.master')
@section('payments', 'active')
@section('withdraws', 'active')
@section('title')
    {{ __('payout') . ' ' . __('lists') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <div>
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <p class="text-left">{{__('you_have_total')}} {{ $withdraws->total() }} {{__('payouts')}}.</p>
                    </div>
                    @if (hasPermission('withdraw_create'))
                        <div class="oftions-content-right">
                            <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2" id="filterBTN">
                                <i class="las la-filter"></i>
                            </a>
                            @if (@settingHelper('preferences')->where('title', 'create_payment_request')->first()->staff)
                                <a href="{{ route('admin.withdraw.create') }}"
                                    class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                        class="icon la la-plus"></i><span>{{ __('create') . ' ' . __('request')}}</span></a>
                            @else
                                <button class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                        class="icon la la-plus"></i><span>{{ __('add') . ' (' . __('service_unavailable') . ')' }}</span></button>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-lg-12" id="filterSection">
                        <div class="hidden-filter bg-white redious-border p-20 p-sm-30 mb-4">
                            <form action="{{ route('admin.payment.filter') }}" id="filterForm">
                                <div class="row g-gs">
                                    <div class="col-12 col-md-3 col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('merchant') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="current-amount" value="{{ old('amount') }}" name="amount"
                                                hidden>
                                            <span id="parcels"></span>
                                            <span id="merchant_accounts"></span>
                                            <select name="merchant" class="form-control merchant-live-search"
                                                data-url="{{ route('merchant.change') }}" required>
                                            </select>
                                            @if ($errors->has('merchant'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('merchant') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3 col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('status') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="status" class="without_search form-control">
                                                <option value="">{{ __('any_status') }}</option>
                                                <option value="pending">{{ __('pending') }}</option>
                                                <option value="approved">{{ __('approved') }}</option>
                                                <option value="processed">{{ __('processed') }}</option>
                                                <option value="rejected">{{ __('rejected') }}</option>
                                                <option value="cancelled">{{ __('cancelled') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3 col-lg-3 pt-30">
                                        <div class="col-custom d-flex justify-content-start">
                                            <button type="submit" id="filter"
                                                class="btn sg-btn-primary">{{ __('filter') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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


    {{-- modal --}}
    {{-- process payment modal --}}
    <div class="modal fade" tabindex="-1" id="withdraw-process">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('process_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('process-payment') }}" method="POST" class="form-validate is-alter"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="" id="withdraw-process-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('transaction_id') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="transaction_id" class="form-control" id="transaction_id"
                                value="{{ old('transaction_id') }}" required>
                            @if ($errors->has('transaction_id'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('transaction_id') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('account') }} <span class="text-danger">*</span></label>
                            <select class="without_search form-select form-control account-change" name="account" required>
                                <option value="">{{ __('select_account') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>
                                        ({{ __($account->method) }})
                                        @if ($account->method == 'bank')
                                            {{ $account->account_holder_name . ', ' . $account->account_no . ', ' . __($account->bank_name) . ',' . $account->bank_branch }}.
                                        @elseif($account->method == 'cash')
                                            {{ $account->user->first_name . ' ' . $account->user->last_name }}
                                        @else
                                            {{ $account->account_holder_name . ', ' . $account->number . ', ' . __($account->type) }}
                                        @endif
                                    </option>
                                @endforeach

                            </select>
                            <div class="text-success">
                                <p>{{ __('current_payable_balance') }}: <span id="current-balance" class="currency"
                                        data-default-currency="{{ setting('default_currency') }}">{{ format_price(0) }}</span>
                                </p>
                                <input type="hidden" name="payable_balance" id="payable_balance">
                            </div>
                            @if ($errors->has('account'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('account') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="receipt">{{ __('receipt') }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="receipt" name="receipt">
                                <label class="custom-file-label" for="receipt">{{ __('choose_file') }}</label>
                            </div>
                            @if ($errors->has('receipt'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('receipt') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{ __('process') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- approve payment modal --}}
    <div class="modal fade" tabindex="-1" id="withdraw-approve">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('approve_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('approve-payment') }}" method="POST" class="form-validate is-alter"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="nk-tb-item">
                            <input type="hidden" name="id" value="" id="withdraw-approve-id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('add_to_batch') }}</label>
                            <select class=" without_search form-select form-control withdraw_batches change-batch"
                                name="withdraw_batch">
                                <option value="">{{ __('select_batch') }}</option>

                            </select>
                            @if ($errors->has('withdraw_batch'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('withdraw_batch') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{ __('approve') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="batch-change">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('add_change_batch') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update-payment-batch') }}" method="POST" class="form-validate is-alter"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="nk-tb-item">
                            <input type="hidden" name="id" value="" id="change-batch-id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('batch') }}</label>
                            <select class="without_search form-control withdraw_batches" name="withdraw_batch">
                                <option value="">{{ __('select_batch') }}</option>
                            </select>
                            @if ($errors->has('withdraw_batch'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('withdraw_batch') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- reject payment modal --}}
    <div class="modal fade" tabindex="-1" id="payment-reject">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Reject Payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('reject-payment') }}" method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="reject-payment-id">
                        <div class="mb-3">
                            <label class="form-label" for="reject_reason">{{ __('reject_reason') }} <span
                                    class="text-danger">*</span></label>
                            <textarea name="reject_reason" class="form-control"
                                required>{{ old('reject_reason') }}</textarea>

                            @if ($errors->has('reject_reason'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('reject_reason') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{ __('reject') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('admin.withdraws.status-ajax')
    <script>
        var currency = document.getElementsByClassName('currency')[0].dataset.defaultCurrency;

        $(document).ready(function () {
            $('#filterBTN').click(function () {
                $('#filterSection').toggleClass('show');
            });
        });


        $(document).ready(function () {
            const advancedSearchMapping = (attribute) => {

                $('#dataTableBuilder').on('preXhr.dt', function (e, settings, data) {
                    data[attribute.name] = attribute.value;
                });
            }

            $(document).on('change', '#filterForm input', function () {
                advancedSearchMapping({
                    name: $(this).attr('name'),
                    value: $(this).val(),
                });
            });

            $(document).on('change', '#filterForm select', function () {
                advancedSearchMapping({
                    name: $(this).attr('name'),
                    value: $(this).val(),
                });
            });

            $(document).on('click', '#filter', function (event) {
                event.preventDefault();
                $('#dataTableBuilder').DataTable().ajax.reload();
            });
        });
        const refreshDataTable = () => {
            $('#dataTableBuilder').DataTable().ajax.reload();
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.account-change').on('change', function () {

                var token = "{{ csrf_token() }}";
                var url = "{{ url('') }}" + '/admin/get-balance-info';

                var formData = {
                    id: $(this).val()
                }

                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                })
                    .done(function (response) {
                        if (response < 0) {
                            $("#current-balance").parent().removeClass("text-success");
                            $("#current-balance").parent().addClass("text-danger");
                        } else {
                            $("#current-balance").parent().removeClass("text-danger");
                            $("#current-balance").parent().addClass("text-success");
                        }

                        $("#current-balance").html(currency + response);
                        $("#payable_balance").val(response);

                    })
                    .fail(function (error) {
                        Swal.fire('{{ __('opps') }}...',
                            '{{ __('something_went_wrong_with_ajax') }}', 'error');
                    })

            });
        });
    </script>
@endpush
@include('live_search.merchants')
@include('admin.withdraws.status-ajax')