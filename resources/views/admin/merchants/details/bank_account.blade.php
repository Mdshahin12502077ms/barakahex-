@extends('backend.layouts.master')

@section('title')
    {{ __('merchant') . ' ' . __('bank_account') }}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="default-tab-list default-tab-list-v2">
                            <ul class="nav pb-12 mb-20" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link bank-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">{{ __('bank_account') }}</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link bank-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">{{ __('others_account') }}</a>
                                </li>
                                </ul>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                                        <div class="d-flex justify-content-between align-items-center my-12">
                                            <div>
                                                <h5>{{__('bank_information')}}</h5>
                                                <div>
                                                    <p>{{__('bank_info_message')}}</p>
                                                </div>
                                            </div>
                                            @if (hasPermission('merchant_payment_account_update'))
                                                <div class="d-flex">
                                                    <a href="{{ route('detail.merchant.payment.bank.edit', $merchant->id) }}"
                                                        class="d-flex align-items-center btn sg-btn-primary gap-1"><i class="las la-edit"></i><span>{{ __('edit') }}</span></a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-nowrap table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>{{ __('selected_bank') }}</td>
                                                        <td>{{ __(@$bank->paymentAccount->name) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('bank_branch') }}</td>
                                                        <td>{{ __(@$bank->bank_branch) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('bank_ac_name') }}</td>
                                                        <td>{{ __(@$bank->bank_ac_name) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('bank_ac_number') }}</td>
                                                        <td>{{ __(@$bank->bank_ac_number) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('routing_no') }}</td>
                                                        <td>{{ __(@$bank->routing_no) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="">
                                                <div class="d-flex justify-content-between align-items-center my-12">
                                                    <div>
                                                        <h5>{{__('others_account_information')}}</h5>
                                                        <div>
                                                            <p>{{__('others_account_info_message')}}</p>
                                                        </div>
                                                    </div>
                                                    @if (hasPermission('merchant_payment_account_update'))
                                                        <div class="d-flex">
                                                            <a href="{{ route('detail.merchant.payment.others.edit', $merchant->id) }}"
                                                                class="d-flex align-items-center btn sg-btn-primary gap-1"><i class="las la-edit"></i><span>{{ __('edit') }}</span></a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-nowrap table-responsive mt-4">
                                                <table class="table">
                                                    <tbody>
                                                        @foreach($payments as $payment)
                                                        @if ($payment->mfs_number)
                                                            <tr>
                                                                <td><span
                                                                    class="data-label">{{ __(@$payment->paymentAccount->name) . ' ' . __('number:') }}</span>
                                                                <span
                                                                    class="data-value">{{ $payment->mfs_number }}</span></td>
                                                                <td><span
                                                                    class="data-label">{{ __(@$payment->paymentAccount->name) . ' ' . __('account_type:') }}</span>
                                                                <span
                                                                    class="data-value">{{ __($payment->mfs_ac_type) }}</span></td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
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
    </section>
@endsection

