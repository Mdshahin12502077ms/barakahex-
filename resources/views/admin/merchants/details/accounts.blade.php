@extends('backend.layouts.master')

@section('title')
    {{ __('others_account') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                <h4 class="section-title">{{ __('others_account_informations') }}</h4>
                                <div class="oftions-content-right">
                                    <div class="nk-block-des">
                                        <p>{{ __('others_account_info_message') }}</p>
                                    </div>
                                </div>
                                @if (hasPermission('merchant_payment_account_update'))
                                    <div class="d-flex">
                                        <a href="{{ route('detail.merchant.payment.others.edit', $merchant->id) }}"
                                            class="btn sg-btn-primary d-md-inline-flex"><i
                                                class="icon las la-edit"></i><span>{{ __('edit') }}</span></a>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="nk-data data-list">
                                    <table class="table">
                                        <tr>
                                            <td>{{ __('bkash') . ' ' . __('number') }}</td>
                                            <td>{{ $payment_account->bkash_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('bkash') . ' ' . __('account_type') }}</td>
                                            <td>{{ __($payment_account->bkash_ac_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('rocket') . ' ' . __('number') }}</td>
                                            <td>{{ $payment_account->rocket_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('rocket') . ' ' . __('account_type') }}</td>
                                            <td>{{ __($payment_account->rocket_ac_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('nogod') . ' ' . __('number') }}</td>
                                            <td>{{ $payment_account->nogod_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('nogod') . ' ' . __('account_type') }}</td>
                                            <td>{{ __($payment_account->nogod_ac_type) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @include('admin.merchants.details.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
