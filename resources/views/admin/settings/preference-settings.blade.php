@extends('backend.layouts.master')

@section('title')
    {{ __('preference') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="col-xxl-9 col-lg-8 col-md-8">
                        <div class="bg-white redious-border p-20 p-sm-30">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-inner">
                                        <div class="">
                                            <h5>{{ __('services_and_permissions') }}</h5>
                                        </div>
                                        <div class="g-gs">
                                            <table class="table table-bordered m-2">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">{{ __('title') }}</th>
                                                        <th scope="col" class="text-center">{{ __('staff') }}</th>
                                                        <th scope="col" class="text-center">{{ __('merchant') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <div class="g-item">
                                                        @foreach (settingHelper('preferences')->whereNotIn('title', ['same_day', 'next_day', 'sub_city', 'sub_urban_area']) as $preference)
                                                            <tr>
                                                                <td>
                                                                    {{ __($preference->title) }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($preference->title != 'read_merchant_api')
                                                                        <div class="custom-checkbox mb-2">
                                                                            <label class="custom-control-label"
                                                                                for="customSwitch-{{ $preference->id }}">
                                                                                <input type="checkbox" data-id="{{ $preference->id }}" data-url="{{ route('admin.preference-status') }}"
                                                                                    class="custom-control-input {{ hasPermission('preference_setting_update') ? 'status-change-for' : '' }}"
                                                                                    {{ $preference->staff == true ? 'checked' : '' }}
                                                                                    value="preference-status/{{ $preference->id }}"
                                                                                    data-change-for="staff"
                                                                                    id="customSwitch-{{ $preference->id }}">
                                                                                <span class="text-capitalize"></span>
                                                                            </label>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="custom-checkbox mb-2">
                                                                        <label class="custom-control-label"
                                                                            for="customSwitchMask2-{{ $preference->id }}">
                                                                            <input type="checkbox" data-id="{{ $preference->id }}" data-url="{{ route('admin.preference-status') }}"
                                                                                class="custom-control-input  {{ hasPermission('preference_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $preference->merchant == true ? 'checked' : '' }}
                                                                                value="preference-status/{{ $preference->id }}+"
                                                                                data-change-for="merchant"
                                                                                id="customSwitchMask2-{{ $preference->id }}">
                                                                            <span class="text-capitalize text-center"
                                                                                style="padding-left: 1px!important"></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </div>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-inner mt-4">
                                        <div class="card-title">
                                            <h5>{{ __('parcel_delivery') }}</h5>
                                        </div>
                                        <div class="g-gs">
                                            <table class="table table-bordered m-2">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">{{ __('title') }}</th>
                                                        <th scope="col" class="text-center">{{ __('staff') }}</th>
                                                        <th scope="col" class="text-center">{{ __('merchant') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <div class="g-item">
                                                        @foreach (settingHelper('preferences')->whereIn('title', ['same_day', 'next_day', 'sub_city', 'sub_urban_area']) as $preference)
                                                            <tr>
                                                                <td>
                                                                    {{ __($preference->title) }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="custom-checkbox mb-2">
                                                                        <label class="custom-control-label"
                                                                            for="customSwitchMask1-{{ $preference->id }}">
                                                                            <input type="checkbox" data-id="{{ $preference->id }}" data-url="{{ route('admin.preference-status') }}"
                                                                                class="custom-control-input {{ hasPermission('preference_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $preference->staff == true ? 'checked' : '' }}
                                                                                value="preference-status/{{ $preference->id }}"
                                                                                data-change-for="staff"
                                                                                id="customSwitchMask1-{{ $preference->id }}">
                                                                            <span class="text-capitalize"></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="custom-checkbox mb-2">
                                                                        <label class="custom-control-label"
                                                                            for="customSwitchMask-{{ $preference->id }}">
                                                                            <input type="checkbox" data-id="{{ $preference->id }}" data-url="{{ route('admin.preference-status') }}"
                                                                                class="custom-control-input {{ hasPermission('preference_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $preference->merchant == true ? 'checked' : '' }}
                                                                                value="preference-status/{{ $preference->id }}+"
                                                                                data-change-for="merchant"
                                                                                id="customSwitchMask-{{ $preference->id }}">
                                                                            <span class="text-capitalize"></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </div>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card-inner mt-4">
                                        <div class="card-title">
                                            <h5>{{ __('label_sticker') }}</h5>
                                        </div>
                                        <div class="g-gs">
                                            <form action="{{ route('setting.store') }}" class="form-validate" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                {{-- <input type="hidden" name="site_lang" value="{{ $lang }}"> --}}
                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <div class="card-inner">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label" for="fv-full-name">{{ __('sticker') }}
                                                                        <span class="text-danger">*</span></label>
                                                                        <select class="without_search form-select form-control weight"
                                                                            name="label_sticker">
                                                                            <option value="">{{ __('select_sticker') }}</option>
                                                                            <option value="cCorrier" {{ settingHelper('label_sticker') == 'cCorrier' ? 'selected' : '' }}>{{ __('cCorrier') }} - {{ '76.2mmX127mm' }}</option>
                                                                            <option value="pathao" {{ settingHelper('label_sticker') == 'pathao' ? 'selected' : '' }}>{{ __('pathao') }} - {{ '101.6mmX76.2mm' }}</option>
                                                                            <option value="default" {{ settingHelper('label_sticker') == 'default' ? 'selected' : '' }}>{{ __('default') }} - {{ '76.2mmX50.8mm' }}</option>
                                                                        </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 text-right mt-2">
                                                        <div class="mb-3">
                                                            <button type="submit"
                                                                class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card-title mt-5 mb-4">
                                        <h5>{{ __('pickup_time_delivery_days') }}</h5>
                                    </div>
                                    <div class="g-gs">
                                        <div class="col-12">
                                            @if (hasPermission('pickup_and_delivery_time_setting_update'))
                                                <form action="{{ route('setting.store') }}" class="form-validate" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                            @endif
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card-inner">
                                                        <div class="row g-gs">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        for="pickup_accept_start">{{ __('pickup_accept_start') }}({{ __('0_24') }})
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="pickup_accept_start"
                                                                        value="{{ settingHelper('pickup_accept_start') }}"
                                                                        name="pickup_accept_start" min="0" max="24"
                                                                        required>
                                                                    @if ($errors->has('pickup_accept_start'))
                                                                        <div class="invalid-feedback help-block">
                                                                            <p>{{ $errors->first('pickup_accept_start') }}</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row g-gs">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        for="pickup_accept_end">{{ __('pickup_accept_end') }}({{ __('0_24') }})
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="pickup_accept_end"
                                                                        value="{{ settingHelper('pickup_accept_end') }}"
                                                                        name="pickup_accept_end" min="0" max="24"
                                                                        required>
                                                                    @if ($errors->has('pickup_accept_end'))
                                                                        <div class="invalid-feedback help-block">
                                                                            <p>{{ $errors->first('pickup_accept_end') }}</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row g-gs">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        for="outside_dhaka_days">{{ __('outside_dhaka_delivery_days') }}
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="outside_dhaka_days"
                                                                        value="{{ settingHelper('outside_dhaka_days') }}"
                                                                        min="0" name="outside_dhaka_days" required>
                                                                    @if ($errors->has('outside_dhaka_days'))
                                                                        <div class="invalid-feedback help-block">
                                                                            <p>{{ $errors->first('outside_dhaka_days') }}</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if (hasPermission('pickup_and_delivery_time_setting_update'))
                                                            <div class="row">
                                                                <div class="col-md-6 text-right mt-2">
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
                                            @if (hasPermission('pickup_and_delivery_time_setting_update'))
                                        </form>
                                    @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@include('admin.preference.change-status-ajax')

@endsection
@push('script')
<script>
    $(document).ready(function(){
        $(document).on('click','.status-change-label', function(){
            var checkbox = $(this).find('input[type="checkbox"]');
            var url = $(this).data('url');
            var label_sticker = $('input[name="label_sticker"]:checked').val();
            var token = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                label_sticker: label_sticker,
                _token: token
            };

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: formData,
                url: url,
                success: function(response) {
                    toastr.clear();
                    if (response.status == 200 || response.status == "success") {
                        if (response.reload) {
                            toastr["success"](response.message);
                            location.reload();
                        } else {
                            toastr["success"](response.message);
                        }
                    } else {
                        checkbox.prop("checked", !checkbox.prop("checked"));
                        toastr["error"](response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr["error"](error);
                }
            });
        });
    });
</script>

@endpush
