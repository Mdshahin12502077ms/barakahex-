@extends('backend.layouts.master')

@section('title')
    {{ __('bank_account') }}
@endsection

@section('mainContent')
    <section class="options">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.profile.setting-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div
                        class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top justify-content-between align-items-center mb-12">
                            <h5>{{ __('mfs_account') }}</h5>
                        </div>
                        @if($methods->count() > 0)
                        <div class="default-tab-list default-tab-list-v2 activeItem-bd-none">
                            <form
                                action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.others.account.update') : route('merchant.staff.others.account.update') }}"
                                class="form-validate" method="POST">
                                @csrf
                                <div class="row g-gs">
                                    <div class="col-md-12">
                                        <div class="card-inner">
                                            @foreach($methods as $method)
                                                <input type="hidden" name="payment_method_id[]"
                                                       value="{{ $method->id }}">
                                                <div class="row g-gs">
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label"
                                                               for="mfs_number">{{ $method->name . ' ' . __('number') }}</label>
                                                        <input type="text" class="form-control" id="mfs_number"
                                                               value="{{ old('mfs_number.' . $loop->index, @$method->payment->mfs_number) }}"
                                                               name="mfs_number[]">
                                                        @if ($errors->has('mfs_number.' . $loop->index))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('mfs_number.' . $loop->index) }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label"
                                                               for="mfs_ac_type">{{ $method->name . ' ' . __('account_type') }}
                                                            <span class="text-danger">*</span></label>
                                                        <select class="without_search form-select form-control"
                                                                name="mfs_ac_type[]">
                                                            <option value="">{{ __('select_type') }}</option>
                                                            @foreach (\Config::get('parcel.account_types') as $type)
                                                                <option
                                                                    value="{{ $type }}" {{ old('mfs_ac_type.' . $loop->index, @$method->payment->mfs_ac_type) == $type ? 'selected' : '' }}>
                                                                    {{ __($type) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('mfs_ac_type.' . $loop->index))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('mfs_ac_type.' . $loop->index) }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
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
                        @else
                            <div class="header-top justify-content-between align-items-center mt-4 mb-12 gap-3">
                                <p>{{__('this_account')}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
