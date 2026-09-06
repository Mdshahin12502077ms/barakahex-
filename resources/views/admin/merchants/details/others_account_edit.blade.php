@extends('backend.layouts.master')

@section('title')
    {{ __('update') . ' ' . __('merchant') . ' ' . __('others_account') }}
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
                                <h5>{{__('others_account_information')}}</h5>
                                <div  class="mb-4">
                                    <p>{{__('others_account_info_message')}}</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="{{ route('detail.merchant.payment.others.update') }}"
                                            class="form-validate" method="POST">
                                            @csrf
                                            @foreach($methods as $key=> $method)
                                            <div class="row">
                                                <input type="hidden" name="payment_method_id[]" value="{{ $method->id }}">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <input type="text" name="merchant_id" hidden
                                                            value="{{ $merchant->id }}">
                                                        <label class="form-label"
                                                            for="mfs_number">{{ __($method->name) . ' ' . __('number') }} </label>
                                                        <div>
                                                            <input type="text" class="form-control" id="mfs_number"
                                                                value="{{ @$method->mfs_number }}"
                                                                name="mfs_number[]">
                                                        </div>
                                                        @error('mfs_number.' . $key)
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $message }}</p>
                                                        </div>
                                                    @enderror

                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                        for="mfs_ac_type">{{$method->name . ' ' . __('account_type') }}
                                                        <span class="text-danger">*</span></label>
                                                        <select class="without_search form-select form-control"
                                                            name="mfs_ac_type[]">
                                                            <option value="">
                                                                {{ __('select_type') }}
                                                            </option>
                                                            @foreach (\Config::get('parcel.account_types') as $type)
                                                                <option value="{{ $type }}" {{ @$method->mfs_ac_type == $type ? 'selected' : '' }}>

                                                                    {{ __($type) }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                        @error('mfs_ac_type.' . $key)
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $message }}</p>
                                                        </div>
                                                    @enderror

                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            <div class="row">
                                                <div class="col-md-12 text-right mt-3">
                                                    <div class="mb-3">
                                                        <button type="submit"
                                                            class="btn sg-btn-primary">{{ __('update') }}</button>
                                                    </div>
                                                </div>
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
