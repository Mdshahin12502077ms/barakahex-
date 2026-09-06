@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('payment_request') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-lg-6 col-md-6">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('edit') . ' ' . __('payment_request') }}</h3>
                    <div class="oftions-content-right pb-12">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.withdraw.update') : route('merchant.staff.withdraw.update') }}"
                    class="form-validate" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-6 mb-3">
                                            <input type="text" name="id" hidden value="{{ $withdraw->id }}">
                                            <label class="form-label"
                                                for="amount">{{ __('amount') }}</label>
                                                <input type="text" class="form-control" id="amount"
                                                    value="{{ old('amount') ?? $withdraw->amount }}" name="amount"
                                                    placeholder="{{ __('amount') }}" readonly required>
                                            @if ($errors->has('amount'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('amount') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row g-gs">
                                        <div class="col-md-6 mb-3mb-3">
                                            <label class="form-label" for="amount">{{ __('payment_to') }} <span class="text-danger">*</span></label>
                                                <select class="without_search form-select form-control" name="withdraw_to" required>
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
                                    </div>
                                    <div class="row g-gs">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="details">{{ __('details') }} </label>
                                            <textarea class="form-control" id="details" placeholder="{{ __('details') . ' (' . __('optional') . ')' }}"
                                                name="details">{{ old('details') ?? $withdraw->account->details }}</textarea>
                                            @if ($errors->has('details'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('details') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 text-right mt-4">
                                            <button type="submit"
                                                class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
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
