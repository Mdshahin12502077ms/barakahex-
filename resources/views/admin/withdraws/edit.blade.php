@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('payment') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('edit') . ' ' . __('payout'). ' ' . __('request') }}  }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.withdraw.update') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{ $withdraw->id }}" name="id">
                    <input type="hidden" value="{{ $withdraw->amount }}" id="withdraw_amount" name="withdraw_amount">
                    <input type="hidden" value="{{ $withdraw->merchant_id }}" name="merchant">
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label
                                            class="form-label">{{ __('merchant') }}({{ __('we_dont_allow_to_change_merchant') }})
                                            <span class="text-danger">*</span></label>
                                        <input type="text" id="current-amount"
                                            value="{{ old('amount') != '' ? old('amount') : $withdraw->amount }}"
                                            name="amount" hidden>
                                        <div>
                                            <input type="text" class="form-control"
                                                value="{{ $withdraw->merchant->user->first_name . ' ' . $withdraw->merchant->user->last_name }} ({{ $withdraw->merchant->company }})"
                                                required disabled>
                                        </div>
                                        @if ($errors->has('merchant'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('merchant') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="amount">{{ __('amount') }}({{ setting('default_currency') }})
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="amount"
                                            value="{{ old('amount') != '' ? old('amount') : $withdraw->amount }}"
                                            placeholder="{{ __('amount') }}" readonly required>
                                        @if ($errors->has('amount'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('amount') }}</p>
                                            </div>
                                        @endif
                                    </div>


                                    <div class="mb-3">
                                        <label class="form-label" for="amount">{{ __('payment_to') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="without_search form-select form-control" id="withdraw_to"
                                            name="withdraw_to" required>
                                            <option value="">{{ __('select_account') }}</option>
                                            @foreach ($payment_account as $account)
                                                <option value="{{ $account }}"
                                                    {{ $account == $withdraw->withdraw_to ? 'selected' : '' }}>
                                                    {{ __($account) }}</option>
                                            @endforeach

                                        </select>
                                        @if ($errors->has('withdraw_to'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('withdraw_to') }}</p>
                                            </div>
                                        @endif
                                    </div>


                                    <div class="preview-block">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1"
                                                name="status" value="processed"
                                                {{ $withdraw->status == 'processed' ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="customCheck1">{{ __('is_processed') }}</label>
                                        </div>
                                    </div>

                                    <div class="g-gs d-none" id="transaction-area">
                                        <div class="mb-3">
                                            <label class="form-label" for="area">{{ __('transaction_id') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" name="transaction_id" class="form-control"
                                                id="transaction_id" value="{{ old('transaction_id') }}" required>
                                            @if ($errors->has('transaction_id'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('transaction_id') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="g-gs d-none" id="account-area">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('account') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="without_search form-select form-control account-change"
                                                id="account" name="account" required>
                                                <option value="">{{ __('select_account') }}</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('account') == $account->id ? 'selected' : '' }}>
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
                                                <p>{{ __('current_payable_balance') }}: <span
                                                        id="balance">{{ format_price(0) }}</span></p>
                                                <input type="hidden" name="current_payable_balance"
                                                    id="current_payable_balance">
                                            </div>
                                            @if ($errors->has('account'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('account') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="g-gs d-none" id="receipt-area">
                                        <div class="mb-3">
                                            <label class="form-label" for="receipt">{{ __('receipt') }}</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="receipt"
                                                    name="receipt">
                                                <label class="custom-file-label"
                                                    for="receipt">{{ __('choose_file') }}</label>
                                            </div>
                                            @if ($errors->has('receipt'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('receipt') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="details">{{ __('details') }} </label>
                                        <textarea class="form-control" id="details" placeholder="{{ __('details') . ' (' . __('optional') . ')' }}"
                                            name="details">{{ old('details') != '' ? old('details') : $withdraw->account->details }}</textarea>
                                        @if ($errors->has('details'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('details') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 text-right mt-4">
                                            <div class="mb-3">
                                                <button type="submit"
                                                    class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#change-merchant').on('change', function() {

                var token = "{{ csrf_token() }}";
                var url = "{{ url('') }}" + '/admin/get-merchant-info';

                var formData = {
                    id: $(this).val(),
                    amount: $('#withdraw_amount').val()
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
                    .done(function(response) {

                        $("#amount").val(response['balance']);
                        $("#current-amount").val(response['balance']);

                        $("#withdraw_to").empty();
                        $("#withdraw_to").append(response['options']);

                    })
                    .fail(function(error) {
                        Swal.fire('{{ __('opps') }}...',
                            '{{ __('something_went_wrong_with_ajax') }}', 'error');
                    })

            });

            $('#customCheck1').on('click', function() {
                if ($(this).is(':checked')) {
                    $("#transaction-area").removeClass("d-none");
                    $("#account-area").removeClass("d-none");
                    $("#receipt-area").removeClass("d-none");

                } else {
                    $("#transaction-area").addClass("d-none");
                    $("#account-area").addClass("d-none");
                    $("#receipt-area").addClass("d-none");
                    $("#transaction_id").removeAttribute("required");
                    $("#account").removeAttribute("required");
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.account-change').on('change', function() {

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
                    .done(function(response) {
                        if (response < 0) {
                            $("#balance").parent().removeClass("text-success");
                            $("#balance").parent().addClass("text-danger");
                        } else {
                            $("#balance").parent().removeClass("text-danger");
                            $("#balance").parent().addClass("text-success");
                        }

                        $("#balance").html(response);
                        $("#current_payable_balance").val(response);

                    })
                    .fail(function(error) {
                        Swal.fire('{{ __('opps') }}...',
                            '{{ __('something_went_wrong_with_ajax') }}', 'error');
                    })

            });
        });
    </script>
@endpush

@include('live_search.merchants')
