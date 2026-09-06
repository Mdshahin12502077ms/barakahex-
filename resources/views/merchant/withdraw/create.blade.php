@extends('backend.layouts.master')
@section('title')
    {{ __('submit') . ' ' . __('payment_request') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-lg-6 col-md-6">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('submit') . ' ' . __('payout_request') }}</h3>
                    <div class="oftions-content-right pb-12">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.withdraw.store') : route('merchant.staff.withdraw.store') }}"
                    class="form-validate" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <input type="text" name="merchant" hidden
                                            value="{{ Sentinel::getUser()->user_type == 'merchant' ? Sentinel::getUser()->merchant->id : Sentinel::getUser()->merchant_id }}">
                                        <label class="form-label"
                                            for="amount">{{ __('amount') }} ({{ setting('default_currency') }})</label>
                                        <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount"
                                            value="{{ $current_payable }}"
                                            placeholder="{{ __('amount') }}"  readonly>
                                        @if ($errors->has('amount'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('amount') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="amount">{{ __('payout_to') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" value="{{ $current_payable }}"
                                            name="amount" hidden>
                                        <select class="without_search form-select form-control @error('withdraw_to') is-invalid @enderror" name="withdraw_to">
                                            <option value="">{{ __('select_account') }}</option>
                                            @foreach ($payment_account as $account)
                                                @if (!empty($account->bank_ac_number) || !empty($account->mfs_number))
                                                    <option value="{{ $account->id }}">
                                                        {{ __(@$account->paymentAccount->name) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('withdraw_to'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('withdraw_to') }}</p>
                                            </div>
                                        @endif
                                        @foreach ($parcels as $parcel)
                                            <input type="text" value="{{ $parcel->id }}" name="parcels[]" hidden>
                                        @endforeach
                                        @foreach ($merchant_accounts as $merchant_account)
                                            <input type="text" value="{{ $merchant_account->id }}"
                                                name="merchant_accounts[]" hidden>
                                        @endforeach
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="details">{{ __('details') }} </label>
                                        <textarea class="form-control" id="details" placeholder="{{ __('details') . ' (' . __('optional') . ')' }}"
                                            name="details">{{ old('details') }}</textarea>
                                        @if ($errors->has('details'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('details') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 text-right mt-4">
                                            <button type="submit"
                                                class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
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
