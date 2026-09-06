@extends('backend.layouts.master')

@section('title')
    {{ __('add') . ' ' . __('merchant_balance_adjustment') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('add') }} {{ __('merchant_balance_adjustment') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}"
                            class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form action="{{ route('merchant.balance.adjustment.store') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('merchants') }} <span
                                                        class="text-danger">*</span></label>
                                                    <select id="merchant-live-search" name="merchant"
                                                        class="form-control merchant-live-search"
                                                        data-url="{{ route('admin.merchant.parcel') }}" required> </select>
                                                @if ($errors->has('merchant'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('merchant') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-full-name">{{ __('amount') }} ({{ setting('default_currency') }})<span
                                                        class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="fv-full-name"
                                                        value="{{ old('amount') }}" name="amount">
                                                @if ($errors->has('amount'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('amount') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('date') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-control-wrap focused">
                                                    <input type="text" class="form-control date-picker" name="date"
                                                        required autocomplete="off">
                                                </div>
                                                @if ($errors->has('date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="note">{{ __('note') }} </label>
                                                    <textarea class="form-control" id="note" placeholder="{{ __('note') . ' (' . __('optional') . ')' }}" name="note">{{ old('note') }}</textarea>
                                                @if ($errors->has('note'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('note') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 text-right mt-4">
                                            <div class="mb-3">
                                                <button type="submit"  class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
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

@include('live_search.merchants')
