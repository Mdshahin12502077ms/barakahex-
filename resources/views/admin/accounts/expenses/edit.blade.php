@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('expense') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20 justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('edit') }} {{ __('expense') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('expenses.update') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{ $expense->id }}" name="id" id="expense_id">
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="note">{{ __('title') }}/{{ __('details') }} <span
                                                class="text-danger">*</span></label>
                                            <textarea class="form-control @error('details') is-invalid @enderror" id="details"  placeholder="{{ __('title') }}/{{ __('details') }}"
                                                name="details">{{ old('details') != '' ? old('title') : $expense->details }}</textarea>
                                        @if ($errors->has('details'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('details') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('date') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="form-control-wrap focused">
                                            <input type="text" class="form-control date-picker @error('date') is-invalid @enderror" name="date"
                                                 autocomplete="off"
                                                value="{{ old('date') != '' ? old('date') : date('Y-m-d', strtotime($expense->date)) }}">
                                            @if ($errors->has('date'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('date') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('account') }} <span
                                                class="text-danger">*</span></label>
                                            <select class="without_search form-control account-change @error('account') is-invalid @enderror"
                                                name="account">
                                                <option value="">{{ __('select_account') }}</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $account->id == $expense->account_id ? 'selected' : '' }}>
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
                                                    id="current-balance" class="currency" data-default-currency="{{ setting('default_currency') }}">{{ setting('default_currency') }}{{ $balance }}</span>
                                            </p>
                                            <input type="number" hidden name="payable_balance" id="payable_balance"
                                                value="{{ $balance }}">
                                        </div>
                                        @if ($errors->has('account'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('account') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="fv-full-name">{{ __('amount') }} ({{ setting('default_currency') }})<span
                                                class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="fv-full-name"
                                                value="{{ old('amount') != '' ? old('amount') : abs($expense->amount) }}"
                                                name="amount">
                                        @if ($errors->has('amount'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('amount') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="transaction_id">{{ __('transaction_id') }}
                                        </label>
                                            <input type="text" class="form-control expense-amount"
                                                id="transaction_id"
                                                value="{{ old('transaction_id') != '' ? old('transaction_id') : $expense->transaction_id }}"
                                                name="transaction_id">
                                        @if ($errors->has('transaction_id'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('transaction_id') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="input_file_div">
                                        <div class="mb-3 mt-2">
                                            <label class="form-label mb-1">{{ __('receipt') }}
                                                @if (!blank($expense->receipt) && file_exists($expense->receipt))
                                                    <a href="{{ static_asset($expense->receipt) }}" target="_blank"> <i
                                                            class="icon  las la-external-link-alt"></i></a>
                                                @endif
                                            </label>

                                            <input class="form-control sp_file_input file_picker" type="file" id="image"
                                                name="receipt">
                                            <div class="invalid-feedback help-block">
                                                <p class="image_error error">{{ $errors->first('image') }}</p>
                                            </div>
                                        </div>
                                        <div class="selected-files d-flex flex-wrap gap-20">
                                            <div class="selected-files-item">
                                                @if ($expense->receipt != null)
                                                    <img class="selected-img" src="{{ static_asset($expense->receipt) }}"
                                                        alt="favicon">
                                                @else
                                                    <img src="{{ static_asset('admin/images/default/user.jpg') }}"
                                                        id="img_profile" class="img-thumbnail user-profile">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-4">
                                            <div class="mb-3">
                                                <button type="submit" class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
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
        var currency = document.getElementsByClassName('currency')[0].dataset.defaultCurrency;

        $(document).ready(function() {
            $('.account-change').on('change', function() {
                var token = "{{ csrf_token() }}";
                var url = "{{ url('') }}" + '/admin/get-balance-info';

                var formData = {
                    id: $(this).val(),
                    row_id: $('#expense_id').val(),
                    purpose: 'update',
                    table_name: 'company_accounts'
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
                            $("#current-balance").parent().removeClass("text-success");
                            $("#current-balance").parent().addClass("text-danger");
                        } else {
                            $("#current-balance").parent().removeClass("text-danger");
                            $("#current-balance").parent().addClass("text-success");
                        }

                        $("#current-balance").html(currency+ response);
                        $("#payable_balance").val(response);

                    })
                    .fail(function(error) {
                        Swal.fire('{{ __('opps') }}...',
                            '{{ __('something_went_wrong_with_ajax') }}', 'error');
                    })

            });
        });
    </script>
@endpush
