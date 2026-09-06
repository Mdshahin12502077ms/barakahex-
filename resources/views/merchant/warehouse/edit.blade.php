@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('warehouse') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('edit') }} {{ __('warehouse') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <div class="oftions-content-right">
                            <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('merchant.warehouse.update',$warehouse->id) }}" class="form-validate" method="POST"
                      enctype="multipart/form-data">
                    <input type="hidden" name="merchant" hidden id="merchant" value="{{ $warehouse->merchant_id }}">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label" for="warehouse_name">{{ __('warehouse_name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('warehouse_name') is-invalid @enderror" id="warehouse_name"
                                               value="{{ old('warehouse_name') ? old('warehouse_name') : $warehouse->name }}" name="warehouse_name">
                                        @if ($errors->has('warehouse_name'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('warehouse_name') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="phone_number">{{ __('phone_number') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                               value="{{ old('phone_number') ? old('phone_number') : $warehouse->phone_number }}"
                                               name="phone_number">
                                        @if ($errors->has('phone_number'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('phone_number') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="email">{{ __('email') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                               value="{{ old('email') ? old('email') : $warehouse->email }}"
                                               name="email">
                                        @if ($errors->has('email'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('email') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('address') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" placeholder="{{ __('address') }}" name="address">{{ old('address') ? old('address') : $warehouse->address }}</textarea>
                                        @if ($errors->has('address'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('address') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-left align-items-center mt-30">
                                        <button type="submit"
                                                class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                        @include('backend.common.loading-btn', [
                                            'class' => 'btn sg-btn-primary',
                                        ])
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
