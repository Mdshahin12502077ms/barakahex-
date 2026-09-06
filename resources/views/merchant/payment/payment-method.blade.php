@extends('backend.layouts.master')
@section('title')
    {{ __('bank_account') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.profile.setting-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top justify-content-between align-items-center mb-12">
                            <h5>{{ __('payout_method') }}</h5>
                            {{-- <p>{{ __('payment_account_info') }}.</p> --}}
                        </div>
                        <div class="default-tab-list default-tab-list-v2 activeItem-bd-none ">
                            <form action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.update.payment.method') : route('merchant.staff.update.payment.method') }}"
                                class="form-validate" method="POST">
                                @csrf
                                <div class="row g-gs">
                                    <div class="col-md-12">
                                        <div class="card-inner">
                                            <div class="row g-gs">
                                                <div class="col-md-6 mb-2">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="account_id">{{ __('default_payout') }} <span
                                                                class="text-danger">*</span></label>
                                                            <select class="without_search form-select form-control method @error('account_id') is-invalid @enderror"
                                                                name="account_id">
                                                                <option value="">{{ __('select_type') }}</option>
                                                                @foreach ($accounts as $account)
                                                                @if (!empty($account->bank_ac_number) || !empty($account->mfs_number))
                                                                    <option value="{{ $account->id }}" {{  $account->id == $merchant->default_account_id ? 'selected' : '' }}>
                                                                        {{ __($account->paymentAccount->name) }}
                                                                    </option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        @if ($errors->has('account_id'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('account_id') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-gs">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label"
                                                        for="withdraw">{{ __('withdraw') }}
                                                        <span class="text-danger">*</span></label>
                                                    <select class="without_search form-select form-control @error('withdraw') is-invalid @enderror"
                                                        name="withdraw">
                                                        <option value="">
                                                            {{ __('as_per_request') }}
                                                        </option>
                                                        <option value="daily" {{  $merchant->withdraw == 'daily' ? 'selected' : '' }}>
                                                            {{ __('daily') }}
                                                        </option>
                                                        <option value="weekly" {{  $merchant->withdraw == 'weekly' ? 'selected' : '' }}>
                                                            {{ __('weekly') }}
                                                        </option>
                                                        <option value="monthly" {{  $merchant->withdraw == 'monthly' ? 'selected' : '' }}>
                                                            {{ __('monthly') }}
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('withdraw'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('withdraw') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-right mt-4">
                                                    <button type="submit"
                                                        class="btn sg-btn-primary">{{ __('update') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
