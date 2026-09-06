@extends('backend.layouts.master')
@section('title', __('dashboard'))
@push('css')
    <link rel="stylesheet" href="{{ static_asset('admin/css/dropzone.min.css') }}">
@endpush
@section('mainContent')
    @foreach ($notices as $notice)
        <div class="example-alert mb-3">
            <div class="alert {{ $notice->alert_class }} alert-icon alert-dismissible">
                <i class="icon las la-exclamation"></i>
                {{ $notice->details }}
                <small><button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button></small>
            </div>
        </div>
    @endforeach
    @if(!empty($no_default_branch))
        <div class="example-alert mb-3">
            <div class="alert alert-warning alert-icon alert-dismissible">
                <i class="icon las la-exclamation-triangle"></i>
                {{ __('please_create_default_branch_first') }}
                <small><button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button></small>
            </div>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-xxl-3 col-xl-3 col-md-12">
                <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="analytics-content mb-1">
                                <h4 class="no-line-braek">{{__('hello')}} {{ \Sentinel::getUser()->first_name . ' ' . \Sentinel::getUser()->last_name }},</h4>
                                <p class="no-line-braek">{{ __('we_re_thrilled_to_have_you_at') }} {{ setting('system_name') }}.</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="profit clr-1">
                                <div class="profit-icon p-4">
                                    <i class="las la-check-double"></i>
                                </div>
                                    <div class="profit-content no-line-braek">
                                        <h4 class="own-balance">{{ setting('default_currency') . $finance_self_wallet }}</h4>
                                        <p>{{ __('current_balance') }}</p>
                                    </div>
                            </div>
                            <div class="text-center no-line-braek">
                            <a href="{{ route('admin.account') }}" class="btn btn-sm sg-btn-primary gap-1 px-4 mt-20 mb-20">
                                <span>{{__('manage_account')}}</span>
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-9 col-xl-9 col-md-12">
                <div class="row">
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="analytics clr-4">
                                        <div class="analytics-icon">
                                            <i class="las la-receipt"></i>
                                        </div>

                                        <div class="analytics-content no-line-braek">
                                            <h4>{{ format_price($staff_balance + $deliveryman_balance) }}</h4>
                                            <p>{{__('balance')}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="statistics-gChart mb-20 mb-lg-0">
                                        <canvas id="statisticsChart2"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="analytics clr-1">
                                        <div class="analytics-icon">
                                            <i class="las la-wallet"></i>
                                        </div>
                                        <div class="analytics-content no-line-braek">
                                            <h4>{{ setting('default_currency') . $life_time_total_cod }}</h4>
                                            <p>{{__('cod')}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="statistics-gChart mb-20 mb-lg-0">
                                        <canvas id="statisticsChart1"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="analytics clr-3">
                                        <div class="analytics-icon">
                                            {{-- <i class="la-store-alt"></i> --}}
                                            <i class="las la-store"></i>
                                        </div>

                                        <div class="analytics-content no-line-braek">
                                            <h4>{{ format_price($total_charge) }}</h4>
                                            <p>{{__('charge')}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="statistics-gChart mb-20 mb-lg-0">
                                        <canvas id="statisticsChart5"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-5">
                                <div class="analytics-icon">
                                    <i class="la la-biking"></i>
                                </div>
                                <div class="analytics-content no-line-braek">
                                    <h4>{{ format_price($deliveryman_balance) }}</h4>
                                    <p>{{__('deliveryman_balance')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="analytics clr-2">
                                <div class="analytics-icon">
                                    <i class="las la-check-circle"></i>
                                </div>

                                <div class="analytics-content no-line-braek">
                                    <h4>{{ format_price($pending_payout) }}</h4>
                                    <p>{{__('payout_pending')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="statistics-card bg-white redious-border mb-4 p-20 p-sm-30">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="return clr-3">
                                        <div class="return-icon">
                                            <i class="las la-credit-card"></i>
                                        </div>

                                        <div class="return-content no-line-braek">
                                            <h4 class="red-line">{{ $merchant_balance }}</h4>
                                            <p>{{__('merchant_balance')}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="statistics-gChart mb-20 mb-lg-0">
                                        <canvas id="statisticsChart3"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-6 col-md-12">
                <div class="statistics-card bg-white redious-border mb-4 pt-20 p-30 flex-column h-100 d-flex">
                    <div class="section-top">
                        <h4>{{ __('parcel_report') }}</h4>
                        <div class="statistics-view dropdown pe-4">
                            <a href="#" class="dropdown-toggle dropdown-item" data-bs-toggle="dropdown" id="today" aria-expanded="false">
                                {{ __('today') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="today" id="today" href="#">
                                        {{ __('today') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="yesterday" id="yesterday" href="#">
                                        {{ __('yesterday') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_7_day" id="last_7_day" href="#">
                                        {{ __('last_7_day') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_14_day" id="last_14_day" href="#">
                                        {{ __('last_14_day') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_month" id="last_month" href="#">
                                        {{ __('last_month') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_6_month" id="last_6_month" href="#">
                                        {{ __('last_6_month') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="this_year" id="this_year" href="#">
                                        {{ __('this_year') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_12_month" id="last_12_month" href="#">
                                        {{ __('last_12_month') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" data-filter="custom" id="custom" href="#">
                                        {{ __('Custom') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="statistics-report">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="analytics clr-1 mb-40">
                                    <div class="analytics-icon">
                                        <i class="las la-box"></i>
                                    </div>
                                    <div class="analytics-content">
                                        <p>{{ __('new_parcel') }}</p>
                                        <h4 id="new_parcel">{{ $new_parcel . '/' . setting('default_currency') . $new_parcel_cod }}</h4>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="analytics clr-2 mb-40">
                                    <div class="analytics-icon">
                                        <i class="las la-tags"></i>
                                    </div>

                                    <div class="analytics-content">
                                        <p>{{ __('processing_parcel') }}</p>
                                        <h4 id="processing_parcel">{{ $processing_parcel . '/' . setting('default_currency') . $processing_parcel_cod }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="analytics clr-4 mb-40">
                                    <div class="analytics-icon">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g id="Money/funds">
                                                <g id="Vector">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.0639 5.20407C3.96377 5.75414 3.66406 6.32902 3.66406 6.66667C3.66406 7.00432 3.96377 7.5792 5.0639 8.12926C6.10421 8.64942 7.61136 9 9.33073 9C11.0501 9 12.5573 8.64942 13.5976 8.12926C14.6977 7.5792 14.9974 7.00432 14.9974 6.66667C14.9974 6.32902 14.6977 5.75414 13.5976 5.20407C12.5573 4.68392 11.0501 4.33334 9.33073 4.33334C7.61136 4.33334 6.10421 4.68392 5.0639 5.20407ZM4.16947 3.41522C5.54202 2.72895 7.3682 2.33334 9.33073 2.33334C11.2933 2.33334 13.1194 2.72895 14.492 3.41522C15.8047 4.07159 16.9974 5.16337 16.9974 6.66667C16.9974 8.16997 15.8047 9.26175 14.492 9.91812C13.1194 10.6044 11.2933 11 9.33073 11C7.3682 11 5.54202 10.6044 4.16947 9.91812C2.85674 9.26175 1.66406 8.16997 1.66406 6.66667C1.66406 5.16337 2.85674 4.07159 4.16947 3.41522Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.66406 5.66667C3.21635 5.66667 3.66406 6.11438 3.66406 6.66667H1.66406C1.66406 6.11438 2.11178 5.66667 2.66406 5.66667ZM16.9974 6.66667V11.3333C16.9974 12.8366 15.8047 13.9284 14.492 14.5848C13.1194 15.2711 11.2932 15.6667 9.33073 15.6667C7.36819 15.6667 5.54202 15.2711 4.16947 14.5848C2.85674 13.9284 1.66406 12.8366 1.66406 11.3333V6.66667H3.66406V11.3333C3.66406 11.671 3.96377 12.2459 5.0639 12.7959C6.10421 13.3161 7.61136 13.6667 9.33073 13.6667C11.0501 13.6667 12.5572 13.3161 13.5975 12.7959C14.6977 12.2459 14.9974 11.671 14.9974 11.3333V6.66667H16.9974ZM16.9974 6.66667C16.9974 6.11438 16.5497 5.66667 15.9974 5.66667C15.4451 5.66667 14.9974 6.11438 14.9974 6.66667H16.9974Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.66406 10.3333C3.21635 10.3333 3.66406 10.7811 3.66406 11.3333H1.66406C1.66406 10.7811 2.11178 10.3333 2.66406 10.3333ZM16.9974 11.3333V16C16.9974 17.5033 15.8047 18.5951 14.492 19.2514C13.1194 19.9377 11.2932 20.3333 9.33073 20.3333C7.36819 20.3333 5.54202 19.9377 4.16947 19.2514C2.85674 18.5951 1.66406 17.5033 1.66406 16V11.3333H3.66406V16C3.66406 16.3376 3.96377 16.9125 5.0639 17.4626C6.10421 17.9827 7.61136 18.3333 9.33073 18.3333C11.0501 18.3333 12.5572 17.9827 13.5975 17.4626C14.6977 16.9125 14.9974 16.3376 14.9974 16V11.3333H16.9974ZM16.9974 11.3333C16.9974 10.7811 16.5497 10.3333 15.9974 10.3333C15.4451 10.3333 14.9974 10.7811 14.9974 11.3333H16.9974Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.66406 15C3.21635 15 3.66406 15.4477 3.66406 16H1.66406C1.66406 15.4477 2.11178 15 2.66406 15ZM16.9974 16V20.6667C16.9974 22.17 15.8047 23.2617 14.492 23.9181C13.1194 24.6044 11.2932 25 9.33073 25C7.36819 25 5.54202 24.6044 4.16947 23.9181C2.85674 23.2617 1.66406 22.17 1.66406 20.6667V16H3.66406V20.6667C3.66406 21.0043 3.96377 21.5792 5.0639 22.1293C6.10421 22.6494 7.61136 23 9.33073 23C11.0501 23 12.5572 22.6494 13.5975 22.1293C14.6977 21.5792 14.9974 21.0043 14.9974 20.6667V16H16.9974ZM16.9974 16C16.9974 15.4477 16.5497 15 15.9974 15C15.4451 15 14.9974 15.4477 14.9974 16H16.9974Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.66406 19.6667C3.21635 19.6667 3.66406 20.1144 3.66406 20.6667H1.66406C1.66406 20.1144 2.11178 19.6667 2.66406 19.6667ZM16.9974 20.6667V25.3333C16.9974 26.8366 15.8047 27.9284 14.492 28.5848C13.1194 29.2711 11.2932 29.6667 9.33073 29.6667C7.36819 29.6667 5.54202 29.2711 4.16947 28.5848C2.85674 27.9284 1.66406 26.8366 1.66406 25.3333V20.6667H3.66406V25.3333C3.66406 25.671 3.96377 26.2459 5.0639 26.7959C6.10421 27.3161 7.61136 27.6667 9.33073 27.6667C11.0501 27.6667 12.5572 27.3161 13.5975 26.7959C14.6977 26.2459 14.9974 25.671 14.9974 25.3333V20.6667H16.9974ZM16.9974 20.6667C16.9974 20.1144 16.5497 19.6667 15.9974 19.6667C15.4451 19.6667 14.9974 20.1144 14.9974 20.6667H16.9974Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M18.3972 14.5374C17.2971 15.0875 16.9974 15.6624 16.9974 16C16.9974 16.3376 17.2971 16.9125 18.3972 17.4626C19.4375 17.9828 20.9447 18.3333 22.6641 18.3333C24.3834 18.3333 25.8906 17.9828 26.9309 17.4626C28.031 16.9125 28.3307 16.3376 28.3307 16C28.3307 15.6624 28.031 15.0875 26.9309 14.5374C25.8906 14.0173 24.3834 13.6667 22.6641 13.6667C20.9447 13.6667 19.4375 14.0173 18.3972 14.5374ZM17.5028 12.7486C18.8754 12.0623 20.7015 11.6667 22.6641 11.6667C24.6266 11.6667 26.4528 12.0623 27.8253 12.7486C29.1381 13.4049 30.3307 14.4967 30.3307 16C30.3307 17.5033 29.1381 18.5951 27.8253 19.2515C26.4528 19.9377 24.6266 20.3333 22.6641 20.3333C20.7015 20.3333 18.8754 19.9377 17.5028 19.2515C16.1901 18.5951 14.9974 17.5033 14.9974 16C14.9974 14.4967 16.1901 13.4049 17.5028 12.7486Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M15.9974 15C16.5497 15 16.9974 15.4477 16.9974 16H14.9974C14.9974 15.4477 15.4451 15 15.9974 15ZM30.3307 16V20.6667C30.3307 22.17 29.138 23.2617 27.8253 23.9181C26.4528 24.6044 24.6266 25 22.6641 25C20.7015 25 18.8754 24.6044 17.5028 23.9181C16.1901 23.2617 14.9974 22.17 14.9974 20.6667V16H16.9974V20.6667C16.9974 21.0043 17.2971 21.5792 18.3972 22.1293C19.4376 22.6494 20.9447 23 22.6641 23C24.3834 23 25.8906 22.6494 26.9309 22.1293C28.031 21.5792 28.3307 21.0043 28.3307 20.6667V16H30.3307ZM30.3307 16C30.3307 15.4477 29.883 15 29.3307 15C28.7784 15 28.3307 15.4477 28.3307 16H30.3307Z"
                                                        fill="#FF5630" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M15.9974 19.6667C16.5497 19.6667 16.9974 20.1144 16.9974 20.6667H14.9974C14.9974 20.1144 15.4451 19.6667 15.9974 19.6667ZM30.3307 20.6667V25.3333C30.3307 26.8366 29.138 27.9284 27.8253 28.5848C26.4528 29.2711 24.6266 29.6667 22.6641 29.6667C20.7015 29.6667 18.8754 29.2711 17.5028 28.5848C16.1901 27.9284 14.9974 26.8366 14.9974 25.3333V20.6667H16.9974V25.3333C16.9974 25.671 17.2971 26.2459 18.3972 26.7959C19.4376 27.3161 20.9447 27.6667 22.6641 27.6667C24.3834 27.6667 25.8906 27.3161 26.9309 26.7959C28.031 26.2459 28.3307 25.671 28.3307 25.3333V20.6667H30.3307ZM30.3307 20.6667C30.3307 20.1144 29.883 19.6667 29.3307 19.6667C28.7784 19.6667 28.3307 20.1144 28.3307 20.6667H30.3307Z"
                                                        fill="#FF5630" />
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="analytics-content">
                                        <p>{{ __('delivered') }}</p>
                                        <h4 id="delivered_parcel">{{ $delivered_parcel . '/' . setting('default_currency') . $delivered_parcel_cod}}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="statistics-report-chart">
                        <canvas id="parcelReportChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12">
                <div class="statistics-card bg-white redious-border mb-4 pt-20 p-30 flex-column h-100 d-flex">
                    <div class="section-top">
                        <h4>{{ __('income_statistics') }}</h4>
                        <div class="statistics-view dropdown pe-4">
                            <a href="#" class="dropdown-toggle dropdown-item" data-bs-toggle="dropdown" id="income_today" aria-expanded="false">
                                {{ __('today') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="today" id="income_today" href="#">
                                        {{ __('today') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="yesterday" id="income_yesterday" href="#">
                                        {{ __('yesterday') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="last_7_day" id="income_last_7_day" href="#">
                                        {{ __('last_7_day') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="last_14_day" id="income_last_14_day" href="#">
                                        {{ __('last_14_day') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="last_month" id="income_last_month" href="#">
                                        {{ __('last_month') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="last_6_month" id="income_last_6_month" href="#">
                                        {{ __('last_6_month') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="this_year" id="income_this_year" href="#">
                                        {{ __('this_year') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="last_12_month" id="income_last_12_month" href="#">
                                        {{ __('last_12_month') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item __js_income_filterable_item" data-filter="custom" id="customIncome" href="#">
                                        {{ __('Custom') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="statistics-report custom-report">
                        <div class="row">
                        </div>
                    </div>
                    <div class="statistics-report-chart custom-report">
                        <canvas id="income_statistic" class="income-custom-report"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-lg-12 col-xxl-6">
                <div class="statistics-card bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-2">
                        <h4>{{ __('recent_fund_transfer') }}</h4>
                    </div>
                    <div id="best_selling_table_container">
                        <section class="oftions">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table class="table table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-start">{{ __('from_account') }}</th>
                                                            <th class="text-center">{{ __('created_at') }}</th>
                                                            <th class="text-end">{{ __('amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($fund_transfers as $fund_transfer)
                                                            <tr>
                                                                <td class="text-start">
                                                                    <div>
                                                                        @if($fund_transfer->fromAccount->method == 'bank')
                                                                            <span>{{__('name')}}: {{$fund_transfer->fromAccount->account_holder_name}}</span><br>
                                                                            <span>{{__('account_no')}}: {{$fund_transfer->fromAccount->account_no}}</span><br>
                                                                            <span>{{__('bank')}}: {{__($fund_transfer->fromAccount->bank_name)}}</span><br>
                                                                            <span>{{__('branch')}}: {{$fund_transfer->fromAccount->bank_branch}}</span><br>
                                                                        @elseif($fund_transfer->fromAccount->method == 'cash')
                                                                            <span>{{__('name')}}: {{$fund_transfer->fromAccount->user->first_name . ' ' . $fund_transfer->fromAccount->user->last_name}}({{__($fund_transfer->fromAccount->method)}})</span><br>
                                                                            <span>{{__('email')}}: {{$fund_transfer->fromAccount->user->email}}</span><br>
                                                                        @else
                                                                            <span>{{__('name')}}: {{$fund_transfer->fromAccount->account_holder_name}}</span><br>
                                                                            <span>{{__('number')}}: {{$fund_transfer->fromAccount->number}}</span><br>
                                                                            <span>{{__('account_type')}}: {{__($fund_transfer->fromAccount->type)}}</span><br>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div>
                                                                        <span>
                                                                            {{$fund_transfer->date != "" ? date('M d, Y', strtotime($fund_transfer->date)) : ''}} <br>
                                                                            {{date('M d, Y h:i a', strtotime($fund_transfer->created_at))}}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end">
                                                                    <div>
                                                                        <span>{{format_price($fund_transfer->amount)}}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xxl-6">
                <div class="statistics-card bg-white redious-border mb-4 pt-20 p-30">
                    <div class="section-top mb-2">
                        <h4>{{ __('recent_cash_collection_from_delivery_man') }}</h4>
                    </div>
                    <div id="best_selling_table_container">
                        <section class="oftions">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table class="table table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-start">{{ __('account') }}</th>
                                                            <th class="text-center">{{ __('created_at') }}</th>
                                                            <th class="text-end">{{ __('amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cash_collections as $cash_collection)
                                                            <tr>
                                                                <td class="text-start">
                                                                    <div>
                                                                        @if(!blank($cash_collection->account))
                                                                        @if($cash_collection->account->method == 'bank')
                                                                            <span>{{__('name')}}: {{$cash_collection->account->account_holder_name}}</span><br>
                                                                            <span>{{__('account_no')}}: {{$cash_collection->account->account_no}}</span><br>
                                                                            <span>{{__('bank')}}: {{__($cash_collection->account->bank_name)}}</span><br>
                                                                            <span>{{__('branch')}}:{{$cash_collection->account->bank_branch}}</span><br>
                                                                        @elseif($cash_collection->account->method == 'cash')
                                                                            <span>{{__('name')}}: {{$cash_collection->account->user->first_name . ' ' . $cash_collection->account->user->last_name}}({{__($cash_collection->account->method)}})</span><br>
                                                                            <span>{{__('email')}}: {{$cash_collection->account->user->email}}</span><br>
                                                                        @else
                                                                            <span>{{__('name')}}: {{$cash_collection->account->account_holder_name}}</span><br>
                                                                            <span>{{__('number')}}: {{$cash_collection->account->number}}</span><br>
                                                                            <span>{{__('account_type')}}: {{__($cash_collection->account->type)}}</span><br>
                                                                        @endif
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="">
                                                                        {{$cash_collection->date != "" ? date('M d, Y', strtotime($cash_collection->date)) : ''}} <br>
                                                                        {{date('M d, Y h:i a', strtotime($cash_collection->created_at))}}
                                                                    </div>
                                                                </td>
                                                                <td class="text-end">
                                                                    <div>
                                                                        {{format_price($cash_collection->amount)}}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade custom-modal" id="customDateRangeModal" tabindex="-1" aria-labelledby="customDateRangeModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customDateRangeModalLabel">Select Custom Date Range</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="custom_filter" value="custom">

                        <div class="row g-gs">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('start_date') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="form-control-wrap focused">
                                        <input type="text" class="form-control date-picker" name="start_date" id="startDate"
                                            value="{{ old('start_date') }}" autocomplete="off" required
                                            placeholder="{{ __('start_date') }}"
                                            value="{{ request()->get('start_date') }}">
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('start_date') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('end_date') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="form-control-wrap focused">
                                        <input type="text" class="form-control date-picker" name="end_date" id="endDate"
                                            value="{{ old('end_date') }}" autocomplete="off" required
                                            placeholder="{{ __('end_date') }}"
                                            value="{{ request()->get('end_date') }}">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('end_date') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn sg-btn-primary" id="applyDateRange">{{ __('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade custom-modal" id="applyIncomeDateRangeModal" tabindex="-1" aria-labelledby="customDateRangeModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customDateRangeModalLabel">Select Custom Date Range</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="custom_filter" value="custom">

                        <div class="row g-gs">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('start_date') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="form-control-wrap focused">
                                        <input type="text" class="form-control date-picker" name="start_date" id="start_date"
                                            value="{{ old('start_date') }}" autocomplete="off" required
                                            placeholder="{{ __('start_date') }}"
                                            value="{{ request()->get('start_date') }}">
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('start_date') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('end_date') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="form-control-wrap focused">
                                        <input type="text" class="form-control date-picker" name="end_date" id="end_date"
                                            value="{{ old('end_date') }}" autocomplete="off" required
                                            placeholder="{{ __('end_date') }}"
                                            value="{{ request()->get('end_date') }}">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('end_date') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn sg-btn-primary" id="applyIncomeDateRange">{{ __('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="chart_data" value="{{ json_encode($charts) }}">
@endsection
@push('js')
    <script src="{{ static_asset('admin/js/__chart.js') }}"></script>
    <script>
        var ajaxUrl          = "{{ url()->current() }}";
        var currency         = "{{ setting('default_currency') }}";
        var currency_symbol  = "{{ setting('default_currency') }}";

        $(document).ready(function() {
            var chart = {!! json_encode($charts) !!};
            console.log(chart)
            graph(chart);
            $(document).on('click', '.__js_parcel_filterable_item', function(event) {
                event.preventDefault();
                var filterValue = $(this).data('filter');
                var ajaxUrlWithFilter = ajaxUrl + "?filter=" + filterValue;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function(response) {
                        $('#new_parcel').text(response.data.new_parcel + '/' + currency +response.data.new_parcel_cod);
                        $('#processing_parcel').text(response.data.processing_parcel + '/' + currency +response.data.processing_parcel_cod);
                        $('#delivered_parcel').text(response.data.delivered_parcel + '/' + currency +response.data.delivered_parcel_cod);
                        var charts = response.data.charts;
                        graph(charts);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
                $('#today').html($(this).text());

            });
        });

        function graph(charts) {
            var statisticsItem = document.getElementById("parcelReportChart");

            if (statisticsItem && charts) {
                if (window.earningChartBar) {
                    window.earningChartBar.destroy();
                }
                var data = {
                    labels: charts.labels,
                    datasets: [{
                        label: 'New Parcel',
                        backgroundColor: '#3F52E3',
                        data: charts.new_parcel
                    }, {
                        label: 'Processing Parcel',
                        backgroundColor: '#FF204E',
                        data: charts.processing_parcel,
                    }, {
                        label: 'Delivered Parcel',
                        backgroundColor: '#24D6A5',
                        data: charts.delivered_parcel
                    }]
                };

                var options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                };

                var earningChartBar = new Chart(statisticsItem, {
                    type: 'line',
                    data: data,
                    options: options
                });
                window.earningChartBar = earningChartBar;
            }
        }

        $(document).ready(function(){
            $("#custom").click(function(){
                $('#customDateRangeModal').modal('show');
                $('#today').html($(this).text());

            });
            $("#applyDateRange").click(function(){
                var startDate = $("#startDate").val();
                var endDate = $("#endDate").val();
                $('#customDateRangeModal').modal('hide');
            });
        });

        $(document).ready(function() {
            $("#applyDateRange").click(function(){
                var startDate = $("#startDate").val();
                var filter    = $("#custom_filter").val();
                var endDate   = $("#endDate").val();

                $('#customDateRangeModal').modal('hide');
                var ajaxUrlWithFilter = ajaxUrl + "?startDate=" + startDate + "&endDate=" + endDate + "&filter=" + filter;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function(response) {
                        $('#new_parcel').text(response.data.new_parcel + '/' + currency +response.data.new_parcel_cod);
                        $('#processing_parcel').text(response.data.processing_parcel + '/' + currency + response.data.processing_parcel_cod);
                        $('#delivered_parcel').text(response.data.delivered_parcel + '/' + currency +response.data.delivered_parcel_cod);
                        var charts = response.data.charts;
                        graph(charts);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });


        // income report
        $(document).ready(function() {
            var chart = {!! json_encode($charts) !!};
            var url = "{{ url()->current() }}";

            incomeGraph(chart);
            $(document).on('click', '.__js_income_filterable_item', function(event) {
                event.preventDefault();
                var filterValue = $(this).data('filter');

                var ajaxUrlWithFilter = url + "?filter=" + filterValue;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function(response) {
                        var incomeChart = response.data.charts;

                        incomeGraph(incomeChart);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
                $('#income_today').html($(this).text());
            });
        });

        function incomeGraph(charts) {
            var statisticsIncomeItem = document.getElementById("income_statistic");

            if (statisticsIncomeItem && charts) {
                if (window.incomeChartBar) {
                    window.incomeChartBar.destroy();
                }

                var data = {
                    labels: charts.labels,
                    datasets: [{
                        label: 'Charge',
                        backgroundColor: '#0088cc',
                        data: charts.charge
                    }, {
                        label: 'Vas Charge',
                        backgroundColor: '#FF5630',
                        data: charts.vas
                    }, {
                        label: 'Cash From Merchant',
                        backgroundColor: '#24D6A5',
                        data: charts.cashFromMerchant
                    }]
                };

                var options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += "{{ setting('default_currency') }}" + context.raw;
                                    return label;
                                }
                            }
                        }
                    }
                };

                window.incomeChartBar = new Chart(statisticsIncomeItem, {
                    type: 'bar',
                    data: data,
                    options: options
                });
            }
        }

        $(document).ready(function(){
            $("#customIncome").click(function(){
                $('#applyIncomeDateRangeModal').modal('show');
                $('#today').html($(this).text());

            });
            $("#applyIncomeDateRange").click(function(){
                var startDate = $("#start_date").val();
                var endDate = $("#end_date").val();
                $('#applyIncomeDateRangeModal').modal('hide');
            });
        });

        $(document).ready(function() {
            var url = "{{ url()->current() }}";

            $("#applyIncomeDateRange").click(function(){
                var startDate = $("#start_date").val();
                var filter    = $("#custom_filter").val();
                var endDate   = $("#end_date").val();

                $('#applyIncomeDateRangeModal').modal('hide');
                var ajaxUrlWithFilter = url + "?startDate=" + startDate + "&endDate=" + endDate + "&filter=" + filter;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function(response) {
                        var charts = response.data.charts;
                        incomeGraph(charts);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });


    </script>
@endpush
