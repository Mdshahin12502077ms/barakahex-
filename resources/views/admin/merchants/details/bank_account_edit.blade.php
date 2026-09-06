@extends('backend.layouts.master')

@section('title')
    {{ __('update') . ' ' . __('merchant') . ' ' . __('bank_account') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div
                        class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div>
                            <div>
                                <h5>{{ __('bank_information') }}</h5>
                                <div>
                                    <p>{{ __('bank_info_message') }}</p>
                                </div>
                                <div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="{{ route('detail.merchant.payment.bank.update') }}"
                                            class="form-validate" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="selected_bank">{{ __('selected_bank') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div>
                                                            <input type="text" name="merchant_id" hidden
                                                                value="{{ $merchant->id }}">
                                                            <select class="without_search form-select form-control"
                                                                name="method_id">
                                                                <option value="">{{ __('select_type') }}</option>
                                                                @foreach ($methods as $key=> $method)
                                                                    <option value="{{ $key }}"
                                                                        {{ $key == @$payment_account->payment_method_id ? 'selected' : '' }}>
                                                                        {{ __($method) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if ($errors->has('selected_bank'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('selected_bank') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="bank_branch">{{ __('bank_branch') }}
                                                        </label>
                                                        <div>
                                                            <input type="text" class="form-control" id="bank_branch"
                                                                value="{{ old('bank_branch') != '' ? old('bank_branch') : @$payment_account->bank_branch }}"
                                                                name="bank_branch"
                                                                placeholder="{{ __('enter_bank_branch') }}" required>
                                                        </div>
                                                        @if ($errors->has('bank_branch'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('bank_branch') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="bank_ac_name">{{ __('bank_ac_name') }} </label>
                                                        <div>
                                                            <input type="text" class="form-control" id="bank_ac_name"
                                                                value="{{ old('bank_ac_name') != '' ? old('bank_ac_name') : @$payment_account->bank_ac_name }}"
                                                                name="bank_ac_name"
                                                                placeholder="{{ __('enter_bank_ac_name') }}" required>
                                                        </div>
                                                        @if ($errors->has('bank_ac_name'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('bank_ac_name') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="bank_ac_number">{{ __('bank_ac_number') }} </label>
                                                        <div>
                                                            <input type="text" class="form-control" id="bank_ac_number"
                                                                value="{{ old('bank_ac_number') != '' ? old('bank_ac_number') : @$payment_account->bank_ac_number }}"
                                                                name="bank_ac_number"
                                                                placeholder="{{ __('enter_bank_ac_number') }}" required>
                                                        </div>
                                                        @if ($errors->has('bank_ac_number'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('bank_ac_number') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="routing_no">{{ __('routing_no') }}
                                                        </label>
                                                        <div>
                                                            <input type="text" class="form-control" id="routing_no"
                                                                value="{{ old('routing_no') != '' ? old('routing_no') : @$payment_account->routing_no }}"
                                                                name="routing_no"
                                                                placeholder="{{ __('enter_routing_no') }}" required>
                                                        </div>
                                                        @if ($errors->has('routing_no'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('routing_no') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-30">
                                                <button type="submit"
                                                    class="btn sg-btn-primary">{{ __('submit') }}</button>
                                                @include('backend.common.loading-btn', [
                                                    'class' => 'btn sg-btn-primary',
                                                ])
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
