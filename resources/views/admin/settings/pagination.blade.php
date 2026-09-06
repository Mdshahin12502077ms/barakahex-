@extends('backend.layouts.master')

@section('title')
    {{ __('pagination') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                <h3 class="section-title">{{ __('item_per_page') }}</h3>
                                <div class="oftions-content-right">
                                    <a href="{{ url()->previous() }}"
                                        class="d-flex align-items-center btn sg-btn-primary gap-2">
                                        <i class="las la-arrow-left"></i>
                                        <span>{{ __('back') }}</span>
                                    </a>
                                </div>
                            </div>
                            @if (hasPermission('pagination_setting_update'))
                                <form action="{{ route('setting.store') }}" class="form-validate" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                            @endif
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-inner">
                                            <div class="row g-gs">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="paginate_all_list">{{ __('web_list') }}({{ __('except_parcel_and_merchant') }})
                                                            <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control" id="paginate_all_list"
                                                            value="{{ settingHelper('paginate_all_list') }}"
                                                            name="paginate_all_list" min="1" required>
                                                        @if ($errors->has('paginate_all_list'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('paginate_all_list') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-gs">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="paginate_parcel_merchant_list">{{ __('web_parcel_merchant_list') }}
                                                            <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control"
                                                            id="paginate_parcel_merchant_list"
                                                            value="{{ settingHelper('paginate_parcel_merchant_list') }}"
                                                            name="paginate_parcel_merchant_list" min="1" required>
                                                        @if ($errors->has('paginate_parcel_merchant_list'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('paginate_parcel_merchant_list') }}
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
                                                            for="paginate_api_list">{{ __('mobile_app_list') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control" id="paginate_api_list"
                                                            value="{{ settingHelper('paginate_api_list') }}"
                                                            name="paginate_api_list" min="1" required>
                                                        @if ($errors->has('paginate_api_list'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('paginate_api_list') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if (hasPermission('pagination_setting_update'))
                                                <div class="row">
                                                    <div class="col-md-6 text-right mt-4">
                                                        <div class="mb-3">
                                                            <button type="submit"
                                                                class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (hasPermission('pagination_setting_update'))
                                </form>
                            @endif
                        </div>
                        @include('admin.settings.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
