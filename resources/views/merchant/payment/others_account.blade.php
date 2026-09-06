@extends('backend.layouts.master')
@section('title')
    {{ __('others_account') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.profile.setting-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <h4 class="section-title">{{ __('others_account_information') }}</h4>
                            <div class="">
                                <div class="oftions-content-right">
                                    <div class="nk-block-des">
                                        <p>{{ __('others_account_info_message') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="nk-data data-list">
                                <form action="{{ route('merchant.others.account.update') }}" class="form-validate"
                                    method="POST">
                                    @csrf
                                    <div class="card">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-inner">
                                                    <div class="row g-gs">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="bkash_number">{{ __('bkash') . ' ' . __('number') }}
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="bkash_number"
                                                                    value="{{ old('bkash_number') != '' ? old('bkash_number') : $payment_account->bkash_number }}"
                                                                    name="bkash_number"
                                                                    placeholder="{{ __('enter_bkash_number') }}">
                                                                @if ($errors->has('bkash_number'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('bkash_number') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="bkash_ac_type">{{ __('bkash') . ' ' . __('account_type') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select form-control"
                                                                    name="bkash_ac_type">
                                                                    <option value="">
                                                                        {{ __('select_type') }}</option>
                                                                    @foreach (\Config::get('parcel.account_types') as $type)
                                                                        <option value="{{ $type }}"
                                                                            {{ old('bkash_ac_type') != '' && $type == old('bkash_ac_type') ? 'selected' : ($type == $payment_account->bkash_ac_type ? 'selected' : '') }}>
                                                                            {{ __($type) }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('bkash_ac_type'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('bkash_ac_type') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row g-gs">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="rocket_number">{{ __('rocket') . ' ' . __('number') }}
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="rocket_number"
                                                                    value="{{ old('rocket_number') != '' ? old('rocket_number') : $payment_account->rocket_number }}"
                                                                    name="rocket_number"
                                                                    placeholder="{{ __('enter_rocket_number') }}">
                                                                @if ($errors->has('rocket_number'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('rocket_number') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="rocket_ac_type">{{ __('rocket') . ' ' . __('account_type') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select form-control"
                                                                    name="rocket_ac_type">
                                                                    <option value="">
                                                                        {{ __('select_type') }}</option>
                                                                    @foreach (\Config::get('parcel.account_types') as $type)
                                                                        <option value="{{ $type }}"
                                                                            {{ old('rocket_ac_type') != '' && $type == old('rocket_ac_type') ? 'selected' : ($type == $payment_account->rocket_ac_type ? 'selected' : '') }}>
                                                                            {{ __($type) }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('rocket_ac_type'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('rocket_ac_type') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row g-gs">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="nogod_number">{{ __('nogod') . ' ' . __('number') }}
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="nogod_number"
                                                                    value="{{ old('nogod_number') != '' ? old('nogod_number') : $payment_account->nogod_number }}"
                                                                    name="nogod_number"
                                                                    placeholder="{{ __('enter_nogod_number') }}">
                                                                @if ($errors->has('nogod_number'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('nogod_number') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="nogod_ac_type">{{ __('nogod') . ' ' . __('account_type') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select form-control"
                                                                    name="nogod_ac_type">
                                                                    <option value="">
                                                                        {{ __('select_type') }}</option>
                                                                    @foreach (\Config::get('parcel.account_types') as $type)
                                                                        <option value="{{ $type }}"
                                                                            {{ old('nogod_ac_type') != '' && $type == old('nogod_ac_type') ? 'selected' : ($type == $payment_account->nogod_ac_type ? 'selected' : '') }}>
                                                                            {{ __($type) }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('nogod_ac_type'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('nogod_ac_type') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 text-right mt-4">
                                                            <div class="mb-3">
                                                                <button type="submit"
                                                                    class="btn sg-btn-primary">{{ __('update') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- data-list -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 @endsection
