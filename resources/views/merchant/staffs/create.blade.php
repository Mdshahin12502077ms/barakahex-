@extends('backend.layouts.master')
@section('title')
    {{ __('add') }} {{ __('staff') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('add') }} {{ __('staff') }}</h3>
                    <div class="oftions-content-right pb-12">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form action="{{ route('merchant.staff.store') }}"  method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-12 mb-3">
                                                <label class="form-label" for="fv-full-name">{{ __('first_name') }}
                                                    <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="fv-full-name"
                                                        name="first_name" value="{{ old('first_name') }}">
                                                @if ($errors->has('first_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('first_name') }}</p>
                                                    </div>
                                                @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                                <label class="form-label" for="fv-full-name">{{ __('last_name') }}
                                                    </label>
                                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="fv-full-name"
                                                        name="last_name" value="{{ old('last_name') }}">
                                                    <input type="hidden" class="form-control" name="merchant"
                                                        value="{{ \Sentinel::getUser()->merchant->id }}">
                                                @if ($errors->has('last_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('last_name') }}</p>
                                                    </div>
                                                @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                                <label class="form-label" for="phone_number">{{ __('phone_number') }}
                                                    <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                                        name="phone_number" value="{{ old('phone_number') }}">
                                                @if ($errors->has('phone_number'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('phone_number') }}</p>
                                                    </div>
                                                @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                                <label class="form-label" for="fv-email">{{ __('email') }} <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="fv-email" name="email"
                                                     value="{{ old('email') }}">
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('email') }}</p>
                                                    </div>
                                                @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                                <label class="form-label" for="fv-email">{{ __('password') }} <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="fv-email"
                                                        name="password" value="{{ old('password') }}">
                                                @if ($errors->has('password'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('password') }}</p>
                                                    </div>
                                                @endif
                                        </div>
                                        <div class="col-md-12  input_file_div">
                                            <div class="mb-3 mt-2">
                                                <label class="form-label mb-1">{{ __('profile') }}</label>
                                                <input class="form-control sp_file_input file_picker" type="file" id="profilePhoto"
                                                    name="image_id" accept="image/*">
                                                    @if ($errors->has('image'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('image') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="selected-files d-flex flex-wrap gap-20">
                                                <div class="selected-files-item">
                                                    <img class="selected-img"
                                                     src="{{ getFileLink('80X80', []) }}"
                                                     alt="favicon">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-inner">
                                    <table class="table role-create-table role-permission"
                                    id="permissions-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('modules') }}</th>
                                                <th scope="col">{{ __('permission') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="text-capitalize">{{ __('parcel') }}</span></td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="manage_parcel">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'manage_parcel' }}" name="permissions[]"
                                                                value="manage_parcel">
                                                            <span>{{ __('allow') }}</span>

                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="all_parcel">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'all_parcel' }}" name="permissions[]"
                                                                value="all_parcel">
                                                                <span>
                                                                    {{ __('allow_all') }}
                                                                </span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{ __('payment') }}</span></td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="manage_payment">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'manage_payment' }}" name="permissions[]"
                                                                value="manage_payment">
                                                                <span>
                                                                    {{ __('allow') }}
                                                                </span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="all_parcel_payment">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'all_parcel_payment' }}" name="permissions[]"
                                                                value="all_parcel_payment">
                                                                <span>
                                                                    {{ __('allow_all') }}
                                                                </span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{ __('logs') }}</span></td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="read_logs">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'read_logs' }}" name="permissions[]"
                                                                value="read_logs">
                                                                <span>
                                                                    {{ __('read_logs') }}
                                                                </span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="all_parcel_logs">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'all_parcel_logs' }}" name="permissions[]"
                                                                value="all_parcel_logs">
                                                                <span>
                                                                    {{ __('all_parcel_logs') }}
                                                                </span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="all_payment_logs">
                                                            <input type="checkbox"
                                                                class="custom-control-input read common-key"
                                                                id="{{ 'all_payment_logs' }}" name="permissions[]"
                                                                value="all_payment_logs">
                                                            <span> {{ __('all_payment_logs') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{ __('others_access') }}</span></td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label"
                                                            for="manage_company_information"><input type="checkbox"
                                                                class="custom-control-input read"
                                                                id="{{ 'manage_company_information' }}"
                                                                name="permissions[]" value="manage_company_information">
                                                            <span> {{ __('manage_company_information') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label"
                                                            for="manage_payment_accounts"><input type="checkbox"
                                                                class="custom-control-input read"
                                                                id="{{ 'manage_payment_accounts' }}" name="permissions[]"
                                                                value="manage_payment_accounts">
                                                            <span>{{ __('manage_payment_accounts') }}</span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="manage_shops"> <input
                                                                type="checkbox" class="custom-control-input read"
                                                                id="{{ 'manage_shops' }}" name="permissions[]"
                                                                value="manage_shops">
                                                            <span> {{ __('manage_shops') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="delivery_charge"><input
                                                                type="checkbox" class="custom-control-input read"
                                                                id="{{ 'delivery_charge' }}" name="permissions[]"
                                                                value="delivery_charge">
                                                            <span> {{ __('delivery_charge') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label"
                                                            for="cash_on_delivery_charge"><input type="checkbox"
                                                                class="custom-control-input read"
                                                                id="{{ 'cash_on_delivery_charge' }}" name="permissions[]"
                                                                value="cash_on_delivery_charge">
                                                            <span>{{ __('cash_on_delivery_charge') }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{ __('shops_access') }}</span></td>
                                                <td>
                                                    @foreach (Sentinel::getUser()->merchant->shops as $shop)
                                                        <div class="custom-control custom-checkbox">
                                                            <label class="custom-control-label"
                                                                for="shop-{{ $shop->id }}"> <input type="checkbox"
                                                                    class="custom-control-input read"
                                                                    id="shop-{{ $shop->id }}" name="shops[]"
                                                                    value="{{ $shop->id }}">
                                                                <span>{{ $shop->shop_name . ' (' . $shop->address . ')' }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    @if ($errors->has('shops'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('shops') }}</p>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-4">
                                                <button type="submit" class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
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
@include('admin.roles.script')
