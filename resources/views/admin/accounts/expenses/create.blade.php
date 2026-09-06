@extends('backend.layouts.master')

@section('title')
    {{ __('add') . ' ' . __('expense') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('add') }} {{ __('expense') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('expenses.store') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('title') }}/{{ __('details') }}
                                            <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('details') is-invalid @enderror" id="details" placeholder="{{ __('title') }}/{{ __('details') }}" name="details"
                                            >{{ old('details') }}</textarea>
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
                                            <input type="text" class="form-control date-picker @error('date') is-invalid @enderror" {{ old('date') }} name="date"
                                                autocomplete="off">
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
                                        <select class="without_search  form-control account-change @error('account') is-invalid @enderror" name="account">
                                            <option value="">{{ __('select_account') }}</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
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
                                                    id="current-balance">{{ setting('default_currency') }}0.00</span></p>
                                            <input type="hidden" name="payable_balance" class="currency" data-default-currency="{{ setting('default_currency') }}" id="payable_balance">
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
                                        <input type="number" class="form-control expense-amount @error('amount') is-invalid @enderror" id="fv-full-name"
                                            value="{{ old('amount') }}" name="amount">
                                        @if ($errors->has('amount'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('amount') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="transaction_id">{{ __('transaction_id') }}</label>
                                        <input type="text" class="form-control expense-amount" id="transaction_id"
                                            value="{{ old('transaction_id') }}" name="transaction_id">
                                        @if ($errors->has('transaction_id'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('transaction_id') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="input_file_div">
                                        <div class="mb-3 mt-2">
                                            <label class="form-label mb-1">{{ __('receipt') }}</label>
                                            <input class="form-control sp_file_input file_picker" type="file" id="image" name="receipt">
                                            <div class="invalid-feedback help-block">
                                                <p class="image_error error">{{ $errors->first('image') }}</p>
                                            </div>
                                        </div>
                                        <div class="selected-files d-flex flex-wrap gap-20">
                                            <div class="selected-files-item">
                                                <img class="selected-img" src="{{ getFileLink('80X80', []) }}"
                                                    alt="favicon">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-4">
                                            <div class="mb-3">
                                                <button type="submit"
                                                    class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
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
            $(document).on('change','.account-change',function(e){
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
            })

        });
    </script>
@endpush
