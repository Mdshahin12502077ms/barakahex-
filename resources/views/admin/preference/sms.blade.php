@extends('backend.layouts.master')

@section('title')
    {{ __('sms_preference_setting') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('sms_preference_setting') }}</h3>
                </div>
            </div>
            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-inner">
                            <div class="row g-gs">
                                <div class="col-md-6">
                                    <div class="card-aside-wrap">
                                        <div class="card-inner card-inner-lg">
                                            <div class="header-top">
                                                <h6>{{ __('parcel') . ' ' . __('status') . ' ' . __('sms_to_merchant') }}
                                                </h6>
                                                <p>{{ __('merchant_will_receive_parcel_event_sms') }}</p>

                                            </div>
                                            <div class="gy-3">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('event') }}</th>
                                                            <th scope="col">{{ __('status') }}</th>
                                                            <th scope="col">{{ __('masking') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <div class="g-item">
                                                            @foreach ($sms_templates as $event)
                                                                <tr>
                                                                    <td>
                                                                        {{ __($event->subject) }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="setting-check">
                                                                            <input type="checkbox" data-id="{{ $event->id }}" data-url="{{ route('sms.sms-status') }}"
                                                                                class="custom-control-input {{ hasPermission('sms_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $event->sms_to_merchant ? 'checked' : '' }}
                                                                                value="sms-status/{{ $event->id }}"
                                                                                data-change-for="sms_to_merchant"
                                                                                id="customSwitch-{{ $event->id }}">
                                                                            <label
                                                                                for="customSwitch-{{ $event->id }}"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="setting-check">
                                                                            <input type="checkbox" data-id="{{ $event->id }}" data-url="{{ route('sms.sms-masking-status') }}"
                                                                                class="custom-control-input {{ hasPermission('sms_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $event->masking ? 'checked' : '' }}
                                                                                value="sms-masking-status/{{ $event->id }}"
                                                                                data-change-for="sms_to_merchant"
                                                                                id="customSwitchMask-{{ $event->id }}">
                                                                            <label
                                                                                for="customSwitchMask-{{ $event->id }}"></label>
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
                                </div>
                                <div class="col-md-6">
                                    <div class="card-aside-wrap">
                                        <div class="card-inner card-inner-lg">
                                            <div class="header-top">
                                                <h6>{{ __('parcel') . ' ' . __('status') . ' ' . __('sms_to_customer') }}
                                                </h6>
                                                <p>{{ __('customer_will_receive_parcel_event_sms') }}</p>
                                            </div>
                                            <div class="gy-3">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('event') }}</th>
                                                            <th scope="col">{{ __('status') }}</th>
                                                            <th scope="col">{{ __('masking') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <div class="g-item">
                                                            @foreach ($customer_sms_templates as $event)
                                                                <tr>
                                                                    <td>
                                                                        {{ __($event->subject) }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="setting-check">
                                                                            <input type="checkbox" data-id="{{ $event->id }}" data-url="{{ route('sms.sms-status') }}"
                                                                                class="custom-control-input {{ hasPermission('sms_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $event->sms_to_customer ? 'checked' : '' }}
                                                                                value="sms-status/{{ $event->id }}"
                                                                                data-change-for="sms_to_customer"
                                                                                id="customSwitch2-{{ $event->id }}">
                                                                            <label
                                                                                for="customSwitch2-{{ $event->id }}"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="setting-check">
                                                                            <input type="checkbox" data-id="{{ $event->id }}" data-url="{{ route('sms.sms-masking-status') }}"
                                                                                class="custom-control-input {{ hasPermission('sms_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $event->masking ? 'checked' : '' }}
                                                                                value="sms-masking-status/{{ $event->id }}"
                                                                                data-change-for="sms_to_customer"
                                                                                id="customSwitchMask2-{{ $event->id }}">
                                                                            <label
                                                                                for="customSwitchMask2-{{ $event->id }}"></label>
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
                                </div>
                                <div class="col-md-6">
                                    <div class="card-aside-wrap">
                                        <div class="card-inner card-inner-lg">
                                            <div class="header-top">
                                                <h6>{{ __('payment') . ' ' . __('status') . ' ' . __('sms_to_merchant') }}</h6>
                                                <p>{{ __('merchant_will_receive_withdraw_event_sms') }}</p>
                                            </div>
                                            <div class="gy-3">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('event') }}</th>
                                                            <th scope="col">{{ __('status') }}</th>
                                                            <th scope="col">{{ __('masking') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <div class="g-item">
                                                            @foreach ($withdraw_sms_templates as $event)
                                                                <tr>
                                                                    <td>
                                                                        {{ __($event->subject) }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="setting-check">
                                                                            <input type="checkbox" data-id="{{ $event->id }}" data-url="{{ route('sms.sms-status') }}"
                                                                                class="custom-control-input {{ hasPermission('sms_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $event->sms_to_merchant ? 'checked' : '' }}
                                                                                value="sms-status/{{ $event->id }}"
                                                                                data-change-for="withdraw_sms"
                                                                                id="customSwitch3-{{ $event->id }}">
                                                                            <label
                                                                                for="customSwitch3-{{ $event->id }}"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="setting-check">
                                                                            <input type="checkbox" data-id="{{ $event->id }}" data-url="{{ route('sms.sms-masking-status') }}"
                                                                                class="custom-control-input {{ hasPermission('sms_setting_update') ? 'status-change-for' : '' }}"
                                                                                {{ $event->masking ? 'checked' : '' }}
                                                                                value="sms-masking-status/{{ $event->id }}"
                                                                                data-change-for="withdraw_sms"
                                                                                id="customSwitchMask3-{{ $event->id }}">
                                                                            <label
                                                                                for="customSwitchMask3-{{ $event->id }}"></label>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('admin.preference.change-status-ajax')
