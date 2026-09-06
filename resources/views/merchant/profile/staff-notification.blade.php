@extends('backend.layouts.master')

@section('title')
    {{ __('profile') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div >
                    <div class="card">
                        <div class="card-aside-wrap">
                            <div class="card-inner card-inner-lg">
                                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                    <div class="">
                                        <div class="oftions-content-right">
                                            <h4 class="nk-block-title">Notification Settings</h4>
                                            <div class="nk-block-des">
                                                <p>You will get only notification what have enabled.</p>
                                            </div>
                                        </div>
                                     </div>
                                </div>
                                <div class="header-top d-flex justify-content-between align-items-center">
                                    <div class="oftions-content-right">
                                        <h6>Security Alerts</h6>
                                        <p>You will get only those email notification what you want.</p>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="gy-3">
                                        <div class="g-item">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" checked
                                                    id="unusual-activity">
                                                <label class="custom-control-label" for="unusual-activity">Email me whenever
                                                    encounter unusual activity</label>
                                            </div>
                                        </div>
                                        <div class="g-item">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="new-browser">
                                                <label class="custom-control-label" for="new-browser">Email me if new
                                                    browser is used to sign in</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="header-top d-flex justify-content-between align-items-center">
                                    <div class="oftions-content-right">
                                        <h6>News</h6>
                                        <p>You will get only those email notification what you want.</p>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="gy-3">
                                        <div class="g-item">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" checked
                                                    id="latest-sale">
                                                <label class="custom-control-label" for="latest-sale">Notify me by email
                                                    about sales and latest news</label>
                                            </div>
                                        </div>
                                        <div class="g-item">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="feature-update">
                                                <label class="custom-control-label" for="feature-update">Email me about new
                                                    features and updates</label>
                                            </div>
                                        </div>
                                        <div class="g-item">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" checked
                                                    id="account-tips">
                                                <label class="custom-control-label" for="account-tips">Email me about tips
                                                    on using account</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @include('merchant.profile.profile-sidebar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.profile.profile-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{__('company_information')}}</h5>
                                <div>
                                    <p>{{__('company_info_message')}}</p>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#update-merchant"
                                        class="btn sg-btn-primary d-md-inline-flex align-items-center justify-content-center gap-1"><i class="las la-edit"></i><span>{{ __('edit') }}</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 >{{__('basics')}}</h6>
                                                </td>
                                            </tr>
                                            <tr data-bs-toggle="modal" data-bs-target="#profile-edit">
                                                <td>{{__('company_name')}}</td>
                                                <td>{{ $merchant->company }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('phone')}}</td>
                                                <td>{{ $merchant->phone_number }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('account')}}</td>
                                                <td>
                                                    @if ($merchant->status == \App\Enums\StatusEnum::INACTIVE)
                                                        <span class="tb-status text-info">{{ __('inactive') }}</span>
                                                    @elseif($merchant->status == \App\Enums\StatusEnum::ACTIVE)
                                                        <span class="tb-status text-success">{{ __('active') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('registration_status')}}</td>
                                                <td>
                                                    @if ($merchant->registration_confirmed == 0)
                                                        <span class="tb-status text-info">{{ __('not_confirmed') }}</span>
                                                    @elseif($merchant->status == 1)
                                                        <span class="tb-status text-success">{{ __('confirmed') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('website')}}</td>
                                                <td>
                                                    <a href="{{ $merchant->website }}">
                                                        <span class="data-value">
                                                            <span
                                                                class="tb-status text-info">{{ $merchant->website ?? __('not_available') }}</span>
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('trade_license')}}</td>
                                                <td>
                                                    <a href="{{ $merchant->trade_license }}">
                                                        <span class="data-value">
                                                            <span
                                                                class="tb-status text-info">{{ $merchant->trade_license ? __('trade_license') : __('not_available') }}
                                                                @php
                                                                    if ($merchant->trade_license != '') {
                                                                        echo '<i class="icon  las la-external-link-alt"></i>';
                                                                    }
                                                                @endphp</span>
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('nid')}}</td>
                                                <td>
                                                    <a href="{{ $merchant->nid }}">
                                                        <span class="data-value">
                                                            <span
                                                                class="tb-status text-info">{{ $merchant->nid ? __('nid') : __('not_available') }}
                                                                @php
                                                                    if ($merchant->nid != '') {
                                                                        echo '<i class="icon  las la-external-link-alt"></i>';
                                                                    }
                                                                @endphp</span>
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
