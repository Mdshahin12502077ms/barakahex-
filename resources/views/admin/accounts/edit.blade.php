@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('income') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('edit') }} {{ __('income') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form action="{{ route('incomes.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{ $company_account->id }}" name="id">
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('delivery_man') }} <span
                                                class="text-danger">*</span></label>
                                        <select id="delivery-man-live-search" name="delivery_man"
                                            class="form-control delivery-man-live-search get-delivery-man-balance @error('delivery_man') is-invalid @enderror"
                                            data-for="update" data-id="{{ $company_account->id }}"
                                            data-url="{{ route('get-delivery-man-live') }}" required>
                                            <option value="{{ $company_account->delivery_man_id }}">
                                                {{ $company_account->deliveryMan->user->first_name . ' ' . $company_account->deliveryMan->user->last_name }}
                                            </option>
                                        </select>
                                        <span class="current-balance">
                                            {{ $current_amount }}
                                        </span>
                                        @if ($errors->has('delivery_man'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('delivery_man') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('account') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="without_search  form-control @error('account') is-invalid @enderror"
                                            name="account">
                                            <option value="">{{ __('select_parcel') }}</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" {{ $account->id == $company_account->account_id ? 'selected' : '' }}>
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
                                        @if ($errors->has('account'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('account') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="damount">{{ __('amount') }}
                                            ({{ (setting('default_currency'))  }}) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                            id="damount"
                                            value="{{ old('amount') != '' ? old('amount') : $company_account->amount }}"
                                            name="amount">
                                        @if ($errors->has('amount'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('amount') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control date-picker @error('date') is-invalid @enderror" name="date"
                                            autocomplete="off"
                                            value="{{ old('date') != '' ? old('date') : date('Y-m-d', strtotime($company_account->date)) }}">
                                        @if ($errors->has('date'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('date') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('details') }}</label>
                                        <textarea class="form-control" id="details" placeholder="{{ __('details') }}"
                                            name="details">{{ old('details') != '' ? old('details') : $company_account->details }}</textarea>
                                        @if ($errors->has('details'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('details') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-right mt-4">
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
@include('live_search.delivery-man')
@push('script')
    <script>// Prevent typing more than balance
        $('#damount').on('input', function () {
            var $this = $(this);
            var max = parseFloat($this.attr('max')) || 0; // fallback to 0 if NaN
            var val = parseFloat($this.val()) || 0;

            // Case 1: no available balance
            if (max <= 0) {
                $this.val('');
                $this.prop('readonly', true);
                alert('No available balance to use');
                return; // stop further execution
            }

            // Case 2: user typed more than balance
            if (val > max) {
                $this.val(max); // reset to max
                alert('Available balance is: ' + max);
            }
        });</script>
    <script id="fa" src="{{ static_asset('admin') }}/js/bundle.js?ver=2.3.0"></script>
@endpush