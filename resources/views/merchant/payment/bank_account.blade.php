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
                            <h5>{{ __('bank_account') }}</h5>
                        </div>
                        <form
                            action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.bank.account.update') : route('merchant.staff.bank.account.update') }}"
                            class="form-validate" method="POST">
                            @csrf
                            <div class="row g-gs">

                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="selected_bank">{{ __('selected_bank') }}
                                        <span class="text-danger">*</span></label>
                                    <select class="without_search form-select form-control @error('payment_method_id') is-invalid @enderror" name="payment_method_id">
                                        <option value="">
                                            {{ __('select_type') }}
                                        </option>

                                        @foreach ($methods as $data)
                                            <option value="{{ $data->id }}" {{ $data->id == @$method->payment->payment_method_id ? 'selected' : '' }}
                                                >
                                                {{ __($data->name) }}

                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('payment_method_id'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('payment_method_id') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-gs">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="bank_branch">{{ __('bank_branch') }}
                                    <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('bank_branch') is-invalid @enderror" id="bank_branch"
                                        value="{{ old('bank_branch') != '' ? old('bank_branch') : @$method->payment->bank_branch }}"
                                        name="bank_branch" placeholder="{{ __('enter_bank_branch') }}">
                                    @if ($errors->has('bank_branch'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('bank_branch') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-gs">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="bank_ac_name">{{ __('bank_ac_name') }}
                                    <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('bank_ac_name') is-invalid @enderror" id="bank_ac_name"
                                        value="{{ old('bank_ac_name') != '' ? old('bank_ac_name') : @$method->payment->bank_ac_name }}"
                                        name="bank_ac_name" placeholder="{{ __('enter_bank_ac_name') }}">
                                    @if ($errors->has('bank_ac_name'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('bank_ac_name') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-gs">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="bank_ac_number">{{ __('bank_ac_number') }}
                                    <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('bank_ac_number') is-invalid @enderror" id="bank_ac_number"
                                        value="{{ old('bank_ac_number') != '' ? old('bank_ac_number') : @$method->payment->bank_ac_number }}"
                                        name="bank_ac_number" placeholder="{{ __('enter_bank_ac_number') }}"
                                    >
                                    @if ($errors->has('bank_ac_number'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('bank_ac_number') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-gs">
                                <div class="col-md-6 ">
                                    <label class="form-label" for="routing_no">{{ __('routing_no') }}
                                    <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('routing_no') is-invalid @enderror" id="routing_no"
                                        value="{{ old('routing_no') != '' ? old('routing_no') : @$method->payment->routing_no }}"
                                        name="routing_no" placeholder="{{ __('enter_routing_no') }}">
                                    @if ($errors->has('routing_no'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('routing_no') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-4">
                                    <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
