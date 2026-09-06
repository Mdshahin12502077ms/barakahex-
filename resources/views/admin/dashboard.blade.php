@extends('backend.layouts.master')
@section('title', __('dashboard'))
@push('css')
    <link rel="stylesheet" href="{{ static_asset('admin/css/dropzone.min.css') }}">
@endpush
@section('mainContent')
    <section class="oftions">
        @foreach ($notices as $notice)
            <div class="example-alert mb-3">
                <div class="alert {{ $notice->alert_class }} alert-icon alert-dismissible">
                    <i class="icon las la-exclamation"></i>
                    {{ $notice->details }}
                    <small>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </small>
                </div>
            </div>
        @endforeach
        @if(!empty($no_default_branch))
            <div class="example-alert mb-3">
                <div class="alert alert-warning alert-icon alert-dismissible">
                    <i class="icon las la-exclamation-triangle"></i>
                    {{ __('please_create_default_branch_first') }}
                    <small>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </small>
                </div>
            </div>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
                    <div class="statistics-card bg-white color-success redious-border mb-20 p-20 p-md-20">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="statistics-info mb-3">
                                    <h6>{{ __('total_cod') }}</h6>
                                    <h4>{{ format_price($life_time_total_cod) }}</h4>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="statistics-gChart mb-20 mb-lg-0">
                                    <canvas id="statisticsChart1"></canvas>
                                </div>
                            </div>
                            <div class="statistics-footer d-flex align-items-center gap-3">
                                <p class="sales-price">{{ format_price($total_cod_in_last_30_days) }}</p>
                                <h6>{{ __('since_last_30_days') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- End Statistics Chart1 -->
                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
                    <div class="statistics-card bg-white color-warning redious-border mb-20 p-20 p-sm-20">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="statistics-info mb-3">
                                    <h6>{{ __('total_earning') }}</h6>
                                    <h4>{{ format_price($life_time_total_profit) }}</h4>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="statistics-gChart mb-20 mb-lg-0">
                                    <canvas id="statisticsChart2"></canvas>
                                </div>
                            </div>
                            <div class="statistics-footer d-flex align-items-center gap-3">
                                <p class="sales-price">{{ format_price($monthly_profit) }}</p>
                                <h6>{{ __('since_last_30_days') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Statistics Chart2 -->

                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
                    <div class="statistics-card bg-white color-danger redious-border mb-20 p-20 p-sm-20">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="statistics-info mb-3">
                                    <h6>{{ __('total_merchant') }}</h6>
                                    <h4>{{ $life_time_total_merchant }}</h4>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="statistics-gChart mb-20 mb-lg-0">
                                    <canvas id="statisticsChart3"></canvas>
                                </div>
                            </div>
                            <div class="statistics-footer d-flex align-items-center gap-3">
                                <p class="sales-price">{{ $total_merchant_in_last_30_days }}</p>
                                <h6>{{ __('since_last_30_days') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
                    <div class="statistics-card bg-white color-blue redious-border mb-20 p-20 p-sm-20">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="statistics-info mb-3">
                                    <h6>{{ __('total_parcel') }}</h6>
                                    <h4>{{ $life_time_total_parcel_count }}</h4>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="statistics-gChart mb-20 mb-lg-0">
                                    <canvas id="statisticsChart4"></canvas>
                                </div>
                            </div>
                            <div class="statistics-footer d-flex align-items-center gap-3">
                                <p class="sales-price">{{ $total_parcel_count_in_last_30_days }}</p>
                                <h6>{{ __('since_last_30_days') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Statistics Chart4 -->
            </div>
            <div class="row">
                <div class="col-xl-6 col-md-12">
                    <div class="row">
                        <div class="col-xxl-6 col-md-6">
                            <div class="statistics-card bg-white color-primary redious-border mb-4 p-20 p-sm-20">
                                <div class="income-icon">
                                    <i class="las la-credit-card"></i>
                                </div>
                                <div class="statistics-gChart">
                                    <canvas id="statisticsChart5"></canvas>
                                </div>
                                <div class="statistics-info income-content mt-1">
                                    <h6>{{ __('total_income') }}</h6>
                                    <h4>{{ format_price($life_time_income) }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div class="statistics-card bg-white color-danger redious-border mb-4 p-20 p-sm-20">
                                <div class="statistics-icon">
                                    <svg width="32" height="32" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M318.072 561.493H215.037c-16.962 0-30.72-13.758-30.72-30.72V71.683c0-16.968 13.754-30.72 30.72-30.72h586.598c16.966 0 30.72 13.752 30.72 30.72v459.09c0 16.962-13.758 30.72-30.72 30.72h-96.143c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48h96.143c39.583 0 71.68-32.097 71.68-71.68V71.683c0-39.591-32.094-71.68-71.68-71.68H215.037c-39.586 0-71.68 32.089-71.68 71.68v459.09c0 39.583 32.097 71.68 71.68 71.68h103.035c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48z" />
                                        <path
                                            d="M291.917 259.95h432.845c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48H291.917c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48z" />
                                        <path
                                            d="M367.155 250.006c5.819-9.699 2.673-22.279-7.026-28.098s-22.279-2.673-28.098 7.026l-91.709 152.863c-16.887 28.125-.345 57.353 32.471 57.353h36.905c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48h-31.445l88.902-148.184zm237.42 148.184c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48H743.88c32.816 0 49.358-29.229 32.468-57.36l-91.706-152.857c-5.819-9.699-18.399-12.845-28.098-7.026s-12.845 18.399-7.026 28.098l88.902 148.184H604.575zm21.643 604.647V827.59a10.25 10.25 0 014.469-8.456l63.156-43.138a51.182 51.182 0 0022.323-42.286v-95.805c0-11.311-9.169-20.48-20.48-20.48s-20.48 9.169-20.48 20.48v95.805c0 3.394-1.667 6.553-4.459 8.459l-63.166 43.145a51.21 51.21 0 00-22.323 42.276v175.247c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48zM298.036 623.641v108.851a51.18 51.18 0 0024.96 43.967l72.952 43.556a10.243 10.243 0 014.99 8.794v174.029c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V828.809a51.204 51.204 0 00-24.945-43.958l-72.967-43.565a10.225 10.225 0 01-4.99-8.794V623.641c0-11.311-9.169-20.48-20.48-20.48s-20.48 9.169-20.48 20.48z" />
                                        <path
                                            d="M716.068 678.681V465.003c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56v126.638c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V465.003c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v213.678c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                        <path
                                            d="M621.689 545.519V418.881c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56v165.55c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48v-165.55c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v126.638c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                        <path
                                            d="M527.876 586.436V372.758c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56V536.26c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V372.758c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v213.678c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                        <path
                                            d="M433.444 582.383V418.881c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56v213.678c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V418.881c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v163.502c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                    </svg>
                                </div>
                                <div class="statistics-gChart">
                                    <canvas id="statisticsChart6"></canvas>
                                </div>
                                <div class="statistics-info">
                                    <h6>{{ __('total_expense') }}</h6>
                                    <h4 class="statistics-info">{{ format_price($life_time_expense) }}</h4>
                                </div>
                            </div>
                        </div>
                        <!-- End Statistics Chart6 -->
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="bg-white redious-border mb-4 pt-20 p-30">
                                        <div class="section-top mb-2">
                                            <h4>{{ __('income') }}</h4>
                                        </div>
                                        <div id="payout_table_container">
                                            <table class="table table-borderless best-selling-courses recent-transactions">
                                                <thead>
                                                    <th class="text-start">{{ __('title') }}</th>
                                                    <th class="text-end">{{ __('amount') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-start">{{ __('total_charge_including_vat') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price(abs($lifetime_profit['total_charge_vat'])) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">{{ __('total_vat') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price($lifetime_profit['total_vat']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">{{ __('fragile_liquid_charge') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price($lifetime_profit['total_fragile_charge']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">{{ __('packaging_charge') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price($lifetime_profit['total_packaging_charge']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start bold">
                                                            <strong>{{ __('total_profit') }}</strong>
                                                        </td>
                                                        <td class="bold text-end">
                                                            <strong>{{ format_price($lifetime_profit['total_profit']) }}</strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="bg-white redious-border mb-4 pt-20 p-30">
                                        <div class="section-top mb-2">
                                            <h4>{{ __('payout') }}</h4>
                                        </div>
                                        <div id="payout_table_container">
                                            <table class="table table-borderless best-selling-courses recent-transactions">
                                                <thead>
                                                    <th class="text-start">{{ __('title') }}</th>
                                                    <th class="text-end">{{ __('amount') }}</th>
                                                </thead>
                                                <tbody>

                                                    <tr>
                                                        <td>{{ __('total_cash_collection') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price($lifetime_profit['total_payable_to_merchant']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>

                                                        <td>{{ __('total_payout_processed') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price($lifetime_profit['processed_payouts']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_payout_pending') }}</td>
                                                        <td class="text-end">
                                                            {{ format_price($lifetime_profit['pending_payouts']) }}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="bold">
                                                            <strong>{{ __('current_payable_with_pending') }}</strong>
                                                        </td>
                                                        <td class="bold text-end">
                                                            <strong>{{ format_price($lifetime_profit['current_payable'])  }}</strong>
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
                <div class="col-xl-6 col-md-12">
                    <div class="statistics-card bg-white redious-border mb-4 pt-20 p-30">
                        <div class="section-top">
                            <h4>{{ __('earning_report') }}</h4>
                            <div class="statistics-view dropdown pe-4">
                                <a href="#" class="dropdown-toggle dropdown-item" data-bs-toggle="dropdown"
                                    id="today_earning" aria-expanded="false">
                                    {{ __('today') }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item" data-filter="today" id="today"
                                            href="#">
                                            {{ __('today') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item" data-filter="yesterday"
                                            id="yesterday" href="#">
                                            {{ __('yesterday') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item" data-filter="last_7_day"
                                            id="last_7_day" href="#">
                                            {{ __('last_7_day') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item" data-filter="last_14_day"
                                            id="last_14_day" href="#">
                                            {{ __('last_14_day') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item" data-filter="since_last_month"
                                            id="since_last_month" href="#">
                                            {{ __('since_last_month') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item"
                                            data-filter="since_last_6_month" id="since_last_6_month" href="#">
                                            {{ __('since_last_6_month') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_earning_filterable_item" data-filter="since_this_year"
                                            id="since_this_year" href="#">
                                            {{ __('since_this_year') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" data-filter="custom" id="custom_earning" href="#">
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
                                            <i class="las la-credit-card"></i>
                                        </div>
                                        <div class="analytics-content">
                                            <p>{{ __('income') }}</p>
                                            <h4 id="total_income">{{ setting('default_currency') . $total_income}}</h4>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="analytic clr-2 mb-40">
                                        <div class="analytic-icon">
                                            <svg width="32" height="32" viewBox="0 0 1024 1024"
                                                xmlns="http://www.w3.org/2000/svg" fill="#ff5630">
                                                <path
                                                    d="M318.072 561.493H215.037c-16.962 0-30.72-13.758-30.72-30.72V71.683c0-16.968 13.754-30.72 30.72-30.72h586.598c16.966 0 30.72 13.752 30.72 30.72v459.09c0 16.962-13.758 30.72-30.72 30.72h-96.143c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48h96.143c39.583 0 71.68-32.097 71.68-71.68V71.683c0-39.591-32.094-71.68-71.68-71.68H215.037c-39.586 0-71.68 32.089-71.68 71.68v459.09c0 39.583 32.097 71.68 71.68 71.68h103.035c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48z"
                                                    fill="#ff5630" />
                                                <path
                                                    d="M291.917 259.95h432.845c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48H291.917c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48z" />
                                                <path
                                                    d="M367.155 250.006c5.819-9.699 2.673-22.279-7.026-28.098s-22.279-2.673-28.098 7.026l-91.709 152.863c-16.887 28.125-.345 57.353 32.471 57.353h36.905c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48h-31.445l88.902-148.184zm237.42 148.184c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48H743.88c32.816 0 49.358-29.229 32.468-57.36l-91.706-152.857c-5.819-9.699-18.399-12.845-28.098-7.026s-12.845 18.399-7.026 28.098l88.902 148.184H604.575zm21.643 604.647V827.59a10.25 10.25 0 014.469-8.456l63.156-43.138a51.182 51.182 0 0022.323-42.286v-95.805c0-11.311-9.169-20.48-20.48-20.48s-20.48 9.169-20.48 20.48v95.805c0 3.394-1.667 6.553-4.459 8.459l-63.166 43.145a51.21 51.21 0 00-22.323 42.276v175.247c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48zM298.036 623.641v108.851a51.18 51.18 0 0024.96 43.967l72.952 43.556a10.243 10.243 0 014.99 8.794v174.029c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V828.809a51.204 51.204 0 00-24.945-43.958l-72.967-43.565a10.225 10.225 0 01-4.99-8.794V623.641c0-11.311-9.169-20.48-20.48-20.48s-20.48 9.169-20.48 20.48z" />
                                                <path
                                                    d="M716.068 678.681V465.003c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56v126.638c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V465.003c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v213.678c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                                <path
                                                    d="M621.689 545.519V418.881c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56v165.55c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48v-165.55c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v126.638c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                                <path
                                                    d="M527.876 586.436V372.758c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56V536.26c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V372.758c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v213.678c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z" />
                                                <path
                                                    d="M433.444 582.383V418.881c0-36.757-29.803-66.56-66.56-66.56h-2.222c-36.757 0-66.56 29.803-66.56 66.56v213.678c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48V418.881c0-14.136 11.464-25.6 25.6-25.6h2.222c14.136 0 25.6 11.464 25.6 25.6v163.502c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z"
                                                    fill="#ff5630" />
                                            </svg>
                                        </div>

                                        <div class="analytic-content">
                                            <p>{{ __('expense') }}</p>
                                            <h4 id="total_expense">{{ setting('default_currency') . $total_expense }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="profit clr-4 mb-40">
                                        <div class="profit-icon">
                                            <i class="las la-receipt"></i>
                                        </div>
                                        <div class="profit-content">
                                            <p>{{ __('profit') }}</p>
                                            <h4 id="total_profit">{{ setting('default_currency') . $total_profit }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="statistics-report-chart">
                            <canvas id="statisticsBarChartEarning"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xxl-9 col-xl-6 col-md-12">
                    <div class="statistics-card bg-white redious-border mb-4 pt-20 p-30">
                        <div class="section-top">
                            <h4>{{ __('parcel_report') }}</h4>
                            <div class="statistics-view dropdown pe-4">
                                <a href="#" class="dropdown-toggle dropdown-item" data-bs-toggle="dropdown" id="todayReport"
                                    aria-expanded="false">
                                    {{ __('today') }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item" data-filter="today" id="today"
                                            href="#">
                                            {{ __('today') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item" data-filter="yesterday"
                                            id="yesterday" href="#">
                                            {{ __('yesterday') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_7_day"
                                            id="last_7_day" href="#">
                                            {{ __('last_7_day') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item" data-filter="last_14_day"
                                            id="last_14_day" href="#">
                                            {{ __('last_14_day') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item" data-filter="since_last_month"
                                            id="since_last_month" href="#">
                                            {{ __('since_last_month') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item"
                                            data-filter="since_last_6_month" id="since_last_6_month" href="#">
                                            {{ __('since_last_6_month') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item __js_parcel_filterable_item" data-filter="since_this_year"
                                            id="since_this_year" href="#">
                                            {{ __('since_this_year') }}
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
                                            <h4 id="new_parcel">
                                                {{ $new_parcel . '/' . setting('default_currency') . $new_parcel_cod }}
                                            </h4>
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
                                            <h4 id="processing_parcel">
                                                {{ $processing_parcel . '/' . setting('default_currency') . $processing_parcel_cod }}
                                            </h4>
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
                                            <h4 id="delivered_parcel">
                                                {{ $delivered_parcel . '/' . setting('default_currency') . $delivered_parcel_cod}}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="statistics-report-chart">
                            <canvas id="parcelOverViewChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-6 col-md-12">
                    <div class="row">
                        <div class="col-xxl-6 col-lg-6 col-md-6">
                            <div class="statistics-card bg-white color-success redious-border mb-4 p-20 p-sm-20">
                                <div class="statistics-icon">
                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 122.88 113.11" style="enable-background:new 0 0 122.88 113.11"
                                        xml:space="preserve">
                                        <style type="text/css">
                                            .st0 {
                                                fill-rule: evenodd;
                                                clip-rule: evenodd;
                                            }
                                        </style>
                                        <g>
                                            <path class="st0"
                                                d="M42.09,16.99c0.43,0.12,0.91,0.07,1.43-0.13l-0.85-4.86c0.32-1.22,0.81-2.17,1.46-2.87 c0.68-0.73,1.53-1.18,2.54-1.38C48,7.65,48.4,8.63,49.74,9.49c4.08,2.6,7.52,3.48,12.56,3.55l-1.04,4.2 c0.33,0.14,0.73,0.18,1.13,0.11c0.81-0.07,1.3,0,1.42,0.26c0.19,0.38,0.02,1.17-0.55,2.45l-2.75,4.53 c-1.02,1.68-2.06,3.37-3.37,4.59c-1.25,1.17-2.79,1.95-4.9,1.95c-1.94,0-3.42-0.76-4.63-1.86c-1.27-1.16-2.29-2.74-3.27-4.3 l-2.45-3.89l0,0l-0.01-0.02c-0.74-1.11-1.13-2.06-1.15-2.79c-0.01-0.24,0.03-0.45,0.11-0.62c0.07-0.15,0.18-0.27,0.32-0.37 C41.39,17.13,41.69,17.03,42.09,16.99L42.09,16.99z M44.34,35.3l4.38,12.87l2.2-7.64l-1.08-1.18c-0.49-0.71-0.59-1.33-0.32-1.86 c0.58-1.16,1.79-0.94,2.92-0.94c1.18,0,2.64-0.22,3.02,1.26c0.12,0.5-0.03,1.01-0.38,1.55l-1.08,1.18l2.2,7.64l3.96-12.87 c2.86,2.57,11.32,3.09,14.47,4.84c1,0.56,1.89,1.26,2.62,2.22c1.1,1.45,1.77,3.34,1.95,5.75l0.66,10.41 c-0.16,1.7-1.12,2.68-3.02,2.83H52.45H27.66c-1.9-0.14-2.86-1.12-3.02-2.83l0.66-10.41c0.18-2.4,0.86-4.3,1.96-5.75 c0.72-0.96,1.62-1.66,2.62-2.22C33.02,38.39,41.48,37.87,44.34,35.3L44.34,35.3z M58,63.95v11.8h43.17v5.59v3.02v13.8h-8.61v-13.8 H58v12.42h-9.18V84.36H13.96v13.8H5.35v-13.8v-3.02v-5.59h43.47v-11.8H58L58,63.95z M96.87,103.57c5.33,0,9.65,4.32,9.65,9.66 s-4.32,9.65-9.65,9.65c-5.33,0-9.66-4.32-9.66-9.65S91.54,103.57,96.87,103.57L96.87,103.57z M9.65,103.57 c5.33,0,9.66,4.32,9.66,9.66s-4.32,9.65-9.66,9.65S0,118.56,0,113.22S4.32,103.57,9.65,103.57L9.65,103.57z M53.41,103.57 c5.33,0,9.65,4.32,9.65,9.66s-4.32,9.65-9.65,9.65c-5.33,0-9.65-4.32-9.65-9.65S48.08,103.57,53.41,103.57L53.41,103.57z M63.99,15.91l0.15-6.26c-0.18-2.58-1.04-4.52-2.39-5.99c-3.33-3.61-9.56-4.54-14.26-2.84c-0.79,0.29-1.54,0.65-2.22,1.08 c-1.94,1.24-3.51,3.03-4.13,5.27c-0.15,0.53-0.25,1.06-0.3,1.58c-0.1,2.19-0.04,4.81,0.11,6.9c-0.23,0.09-0.45,0.19-0.64,0.32 c-0.39,0.26-0.68,0.61-0.87,1.01c-0.18,0.39-0.26,0.83-0.25,1.31c0.03,1.02,0.5,2.25,1.4,3.6l2.45,3.89 c1.03,1.64,2.12,3.32,3.54,4.62c1.48,1.35,3.28,2.27,5.68,2.28c2.57,0.01,4.44-0.94,5.97-2.37c1.46-1.37,2.56-3.15,3.64-4.92 l2.79-4.59c0.02-0.03,0.03-0.06,0.05-0.09l0,0c0.77-1.75,0.93-2.98,0.53-3.8C64.97,16.41,64.56,16.08,63.99,15.91L63.99,15.91z" />
                                            <!-- Truncated for brevity -->
                                        </g>
                                    </svg>
                                </div>

                                <div class=" statistics-gChart">
                                    <canvas id="statisticsChart7"></canvas>
                                </div>
                                <div class="statistics-info mt-1">
                                    <h6>{{ __('total_delivery_man') }}</h6>
                                    <h4 class="statistics-violate-text">{{ $life_time_total_delivery_man }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-lg-6 col-md-6">
                            <div class="statistics-card bg-white color-danger redious-border mb-4 p-20 p-sm-20">
                                <div class="statistics-icon">
                                    <svg version="1.1" id="Layer_1" width="32" height="32"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        x="0px" y="0px" viewBox="0 0 106.53 122.88"
                                        style="enable-background:new 0 0 106.53 122.88" xml:space="preserve">
                                        <style type="text/css">
                                            .st0 {
                                                fill-rule: evenodd;
                                                clip-rule: evenodd;
                                            }
                                        </style>
                                        <g>
                                            <path class="st0"
                                                d="M42.09,16.99c0.43,0.12,0.91,0.07,1.43-0.13l-0.85-4.86c0.32-1.22,0.81-2.17,1.46-2.87 c0.68-0.73,1.53-1.18,2.54-1.38C48,7.65,48.4,8.63,49.74,9.49c4.08,2.6,7.52,3.48,12.56,3.55l-1.04,4.2 c0.33,0.14,0.73,0.18,1.13,0.11c0.81-0.07,1.3,0,1.42,0.26c0.19,0.38,0.02,1.17-0.55,2.45l-2.75,4.53 c-1.02,1.68-2.06,3.37-3.37,4.59c-1.25,1.17-2.79,1.95-4.9,1.95c-1.94,0-3.42-0.76-4.63-1.86c-1.27-1.16-2.29-2.74-3.27-4.3 l-2.45-3.89l0,0l-0.01-0.02c-0.74-1.11-1.13-2.06-1.15-2.79c-0.01-0.24,0.03-0.45,0.11-0.62c0.07-0.15,0.18-0.27,0.32-0.37 C41.39,17.13,41.69,17.03,42.09,16.99L42.09,16.99z M44.34,35.3l4.38,12.87l2.2-7.64l-1.08-1.18c-0.49-0.71-0.59-1.33-0.32-1.86 c0.58-1.16,1.79-0.94,2.92-0.94c1.18,0,2.64-0.22,3.02,1.26c0.12,0.5-0.03,1.01-0.38,1.55l-1.08,1.18l2.2,7.64l3.96-12.87 c2.86,2.57,11.32,3.09,14.47,4.84c1,0.56,1.89,1.26,2.62,2.22c1.1,1.45,1.77,3.34,1.95,5.75l0.66,10.41 c-0.16,1.7-1.12,2.68-3.02,2.83H52.45H27.66c-1.9-0.14-2.86-1.12-3.02-2.83l0.66-10.41c0.18-2.4,0.86-4.3,1.96-5.75 c0.72-0.96,1.62-1.66,2.62-2.22C33.02,38.39,41.48,37.87,44.34,35.3L44.34,35.3z M58,63.95v11.8h43.17v5.59v3.02v13.8h-8.61v-13.8 H58v12.42h-9.18V84.36H13.96v13.8H5.35v-13.8v-3.02v-5.59h43.47v-11.8H58L58,63.95z M96.87,103.57c5.33,0,9.65,4.32,9.65,9.66 s-4.32,9.65-9.65,9.65c-5.33,0-9.66-4.32-9.66-9.65S91.54,103.57,96.87,103.57L96.87,103.57z M9.65,103.57 c5.33,0,9.66,4.32,9.66,9.66s-4.32,9.65-9.66,9.65S0,118.56,0,113.22S4.32,103.57,9.65,103.57L9.65,103.57z M53.41,103.57 c5.33,0,9.65,4.32,9.65,9.66s-4.32,9.65-9.65,9.65c-5.33,0-9.65-4.32-9.65-9.65S48.08,103.57,53.41,103.57L53.41,103.57z M63.99,15.91l0.15-6.26c-0.18-2.58-1.04-4.52-2.39-5.99c-3.33-3.61-9.56-4.54-14.26-2.84c-0.79,0.29-1.54,0.65-2.22,1.08 c-1.94,1.24-3.51,3.03-4.13,5.27c-0.15,0.53-0.25,1.06-0.3,1.58c-0.1,2.19-0.04,4.81,0.11,6.9c-0.23,0.09-0.45,0.19-0.64,0.32 c-0.39,0.26-0.68,0.61-0.87,1.01c-0.18,0.39-0.26,0.83-0.25,1.31c0.03,1.02,0.5,2.25,1.4,3.6l2.45,3.89 c1.03,1.64,2.12,3.32,3.54,4.62c1.48,1.35,3.28,2.27,5.68,2.28c2.57,0.01,4.44-0.94,5.97-2.37c1.46-1.37,2.56-3.15,3.64-4.92 l2.79-4.59c0.02-0.03,0.03-0.06,0.05-0.09l0,0c0.77-1.75,0.93-2.98,0.53-3.8C64.97,16.41,64.56,16.08,63.99,15.91L63.99,15.91z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="statistics-gChart">
                                    <canvas id="statisticsChart8"></canvas>
                                </div>
                                <div class="statistics-info mt-1">
                                    <h6>{{ __('total_branch') }}</h6>
                                    <h4>{{ $life_time_total_branch }}</h4>
                                </div>
                            </div>
                        </div>
                        <!-- End Statistics Chart6 -->
                        <div class="col-lg-12">
                            <div class="bg-white redious-border mb-4 pt-20 p-30">
                                <div class="section-top mb-2">
                                    <h4>{{ __('parcel_overview') }}</h4>
                                </div>
                                <div id="payout_table_container">
                                    <table class="table table-borderless best-selling-courses recent-transactions">
                                        <thead>
                                            <th class="text-start">{{ __('parcel') }}</th>
                                            <th class="text-end">{{ __('total') }}</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start">{{ __('delivered') }}</td>
                                                <td class="text-end">{{ $life_time_delivered_parcel }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">{{ __('partially_delivered') }}</td>
                                                <td class="text-end">{{ $life_time_partially_delivered_parcel }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">{{ __('returned') }}</td>
                                                <td class="text-end">{{ $life_time_return_parcel }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">{{ __('cancelled') }}</td>
                                                <td class="text-end">{{ $life_time_cancel_parcel }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">{{ __('processing') }}</td>
                                                <td class="text-end">{{ $life_time_processing_parcel }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">{{ __('pending') }}</td>
                                                <td class="text-end">{{ $life_time_new_parcel }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start">{{ __('deleted') }}</td>
                                                <td class="text-end">{{ $life_time_deleted_parcel }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-lg-12 col-xxl-6">
                    <div class="bg-white redious-border mb-4 pt-20 p-30">
                        <div class="section-top mb-2">
                            <h4>{{ __('recent_merchant') }}</h4>
                        </div>
                        <div id="best_selling_table_container">
                            <section class="oftions">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table class="table ">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>{{ __('company') }}</th>
                                                                    <th>{{ __('phone') }}</th>
                                                                    <th>{{ __('created_at') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($latest_merchants as $latest_merchant)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>
                                                                            <a
                                                                                href="{{route('detail.merchant.personal.info', $latest_merchant->id)}}">
                                                                                <div
                                                                                    class="user-info-panel d-flex gap-12 align-items-center">
                                                                                    <div class="user-img">
                                                                                        @if(!blank($latest_merchant->user->image_id) && file_exists($latest_merchant->user->image?->image_small_two))
                                                                                            <img src="{{static_asset($latest_merchant->user->image?->image_small_two)}}"
                                                                                                alt="{{$latest_merchant->user->first_name}}">
                                                                                        @else
                                                                                            <img src="{{static_asset('admin/images/default/user40x40.jpg')}}"
                                                                                                alt="{{$latest_merchant->user->first_name}}">
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="user-info">
                                                                                        <h4>{{$latest_merchant->company}}</h4>
                                                                                        <span>{{$latest_merchant->user->first_name . ' ' . $latest_merchant->user->last_name}}</span>|<span>{{$latest_merchant->user->email}}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </td>
                                                                        <td>{{ $latest_merchant->phone_number }}</td>
                                                                        <td><span
                                                                                class="text-nowrap">{{ date("F j, Y", strtotime($latest_merchant->created_at))}}</span><br>{{ date("g:i a", strtotime($latest_merchant->created_at)) }}<span></span>
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
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xxl-6">
                    <div class="bg-white redious-border pt-20 p-30">
                        <div class="section-top mb-2">
                            <h4>{{ __('best_merchant') }}</h4>
                        </div>
                        <div id="best_merchant_container">
                            <section class="oftions">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>{{ __('company') }}</th>
                                                                    <th>{{ __('phone') }}</th>
                                                                    <th>{{ __('created_at') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($top_rank_merchants as $top_rank_merchant)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>
                                                                            <a
                                                                                href="{{route('detail.merchant.personal.info', $top_rank_merchant->id)}}">
                                                                                <div
                                                                                    class="user-info-panel d-flex gap-12 align-items-center">
                                                                                    <div class="user-img">
                                                                                        @if(!blank($top_rank_merchant->user->image) && file_exists($top_rank_merchant->user->image?->image_small_two))
                                                                                            <img src="{{static_asset($top_rank_merchant->user->image?->image_small_two)}}"
                                                                                                alt="{{$top_rank_merchant->user->first_name}}">
                                                                                        @else
                                                                                            <img src="{{static_asset('admin/images/default/user40x40.jpg')}}"
                                                                                                alt="{{$top_rank_merchant->user->first_name}}">
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="user-info">
                                                                                        <h4>{{$top_rank_merchant->company}}</h4>
                                                                                        <span>{{$top_rank_merchant->user->first_name . ' ' . $top_rank_merchant->user->last_name}}</span>|<span>{{$top_rank_merchant->user->email}}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </td>
                                                                        <td>{{ $top_rank_merchant->phone_number }}</td>
                                                                        <td><span
                                                                                class="text-nowrap">{{ date("F j, Y", strtotime($top_rank_merchant->created_at))}}</span><br>{{ date("g:i a", strtotime($top_rank_merchant->created_at)) }}<span></span>
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
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade custom-modal" id="customDateRangeModal" tabindex="-1"
            aria-labelledby="customDateRangeModalLabel" aria-hidden="false">
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
                                            placeholder="{{ __('start_date') }}" value="{{ request()->get('start_date') }}">
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
                                            placeholder="{{ __('end_date') }}" value="{{ request()->get('end_date') }}">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('end_date') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn sg-btn-primary"
                                    id="applyDateRange">{{ __('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade custom-modal" id="customEarningDateRangeModal" tabindex="-1"
            aria-labelledby="customEarningDateRangeModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customDateRangeModalLabel">Select Custom Date Range</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="custom_earning_filter" value="custom">

                        <div class="row g-gs">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('start_date') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="form-control-wrap focused">
                                        <input type="text" class="form-control date-picker" name="start_date"
                                            id="startEarningDate" value="{{ old('start_date') }}" autocomplete="off"
                                            required placeholder="{{ __('start_date') }}"
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
                                        <input type="text" class="form-control date-picker" name="end_date"
                                            id="endEarningDate" value="{{ old('end_date') }}" autocomplete="off" required
                                            placeholder="{{ __('end_date') }}" value="{{ request()->get('end_date') }}">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('end_date') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn sg-btn-primary"
                                    id="applyEarningDateRange">{{ __('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="chart_data" value="{{ json_encode($charts) }}">
@endsection
@push('js')
    <script src="{{ static_asset('admin/js/__chart.js') }}"></script>
    <script>
        var ajaxUrl = "{{ url()->current() }}";
        var currency = "{{ setting('default_currency') }}";
        var currency_symbol = "{{ setting('default_currency') }}";

        $(document).ready(function () {
            var chart = {!! json_encode($charts) !!};
            graph(chart);
            $(document).on('click', '.__js_parcel_filterable_item', function (event) {
                event.preventDefault();
                var filterValue = $(this).data('filter');
                var ajaxUrlWithFilter = "{{ route('parcel.report') }}" + "?filter=" + filterValue;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function (response) {
                        $('#new_parcel').text(response.total_new_parcel + '/' + currency + response.total_new_parcel_cod);
                        $('#processing_parcel').text(response.total_processing_parcel + '/' + currency + response.total_processing_parcel_cod);
                        $('#delivered_parcel').text(response.total_delivered_parcel + '/' + currency + response.total_delivered_parcel_cod);
                        var charts = {
                            new_parcel: response.new_parcel,
                            processing_parcel: response.processing_parcel,
                            delivered_parcel: response.delivered_parcel,
                            labels: response.labels
                        };


                        graph(charts);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
                $('#todayReport').html($(this).text());

            });
        });

        function graph(charts) {
            var statisticsItem = document.getElementById("parcelOverViewChart");
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

        $(document).ready(function () {
            $("#custom").click(function () {
                $('#customDateRangeModal').modal('show');
                $('#todayReport').html($(this).text());

            });
            $("#applyDateRange").click(function () {
                var startDate = $("#startDate").val();
                var endDate = $("#endDate").val();
                $('#customDateRangeModal').modal('hide');
            });
        });

        $(document).ready(function () {
            $("#applyDateRange").click(function () {
                var startDate = $("#startDate").val();
                var filter = $("#custom_filter").val();
                var endDate = $("#endDate").val();

                $('#customDateRangeModal').modal('hide');
                var ajaxUrlWithFilter = "{{ route('parcel.report') }}" + "?startDate=" + startDate + "&endDate=" + endDate + "&filter=" + filter;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function (response) {
                        $('#new_parcel').text(response.total_new_parcel + '/' + currency + response.total_new_parcel_cod);
                        $('#processing_parcel').text(response.total_processing_parcel + '/' + currency + response.total_processing_parcel_cod);
                        $('#delivered_parcel').text(response.total_delivered_parcel + '/' + currency + response.total_delivered_parcel_cod);
                        var charts = {
                            new_parcel: response.new_parcel,
                            processing_parcel: response.processing_parcel,
                            delivered_parcel: response.delivered_parcel,
                            labels: response.labels
                        };


                        graph(charts);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });

        //earning
        $(document).ready(function () {
            var chart = {!! json_encode($charts) !!};

            graphs(chart);
            $(document).on('click', '.__js_earning_filterable_item', function (event) {
                event.preventDefault();
                var filter = $(this).data('filter');
                var ajaxUrlEarningFilter = "{{ route('earning.report') }}" + "?filter=" + filter;

                $.ajax({
                    url: ajaxUrlEarningFilter,
                    method: 'GET',
                    success: function (response) {
                        $('#total_income').text(currency_symbol + response.totals.total_income);
                        $('#total_expense').text(currency_symbol + response.totals.total_expense);
                        $('#total_profit').text(currency_symbol + response.totals.total_profit);
                        var charts = response.monthly_data;
                        graphs(charts);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
                $('#today_earning').html($(this).text());

            });
        });

        function graphs(charts) {
            var statisticsItemEarning = document.getElementById("statisticsBarChartEarning");
            if (statisticsItemEarning && charts) {
                if (window.earningChartBar1) {
                    window.earningChartBar1.destroy();
                }

                var data = {
                    labels: charts.labels,
                    datasets: [{
                        label: 'Income',
                        backgroundColor: '#3F52E3',
                        data: charts.income
                    }, {
                        label: 'Expense',
                        backgroundColor: '#FF5630',
                        data: charts.expense,
                    }, {
                        label: 'Profit',
                        backgroundColor: '#24D6A5',
                        data: charts.profit
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

                var earningChartBar1 = new Chart(statisticsItemEarning, {
                    type: 'line',
                    data: data,
                    options: options
                });
                window.earningChartBar1 = earningChartBar1;
            }
        }

        $(document).ready(function () {
            $("#custom_earning").click(function () {
                $('#customEarningDateRangeModal').modal('show');
                $('#today_earning').html($(this).text());

            });
            $("#applyEarningDateRange").click(function () {
                var startDate = $("#startEarningDate").val();
                var endDate = $("#endEarningDate").val();
                $('#customEarningDateRangeModal').modal('hide');
            });
        });

        $(document).ready(function () {
            $("#applyEarningDateRange").click(function () {

                var startDate = $("#startEarningDate").val();
                var filter = $("#custom_earning_filter").val();
                var endDate = $("#endEarningDate").val();

                $('#customEarningDateRangeModal').modal('hide');
                var ajaxUrlWithFilter = "{{ route('earning.report') }}" + "?startDate=" + startDate + "&endDate=" + endDate + "&filter=" + filter;

                $.ajax({
                    url: ajaxUrlWithFilter,
                    method: 'GET',
                    success: function (response) {
                        $('#total_income').text(currency_symbol + response.totals.total_income);
                        $('#total_expense').text(currency_symbol + response.totals.total_expense);
                        $('#total_profit').text(currency_symbol + response.totals.total_profit);
                        var charts = response.monthly_data;
                        graphs(charts);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });

    </script>
@endpush