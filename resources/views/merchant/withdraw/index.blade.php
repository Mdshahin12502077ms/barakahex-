@extends('backend.layouts.master')
@section('title')
    {{ __('payout') . ' ' . __('lists') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <p>{{ __('you_have_total') }} {{ $withdraws->total() }} {{ __('payout') }}.</p>
                    </div>
                    <div class="oftions-content-right mb-12">
                        @if (Sentinel::getUser()->user_type == 'merchant')
                            <a href="{{ route('merchant.statements') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                                <span><i class="icon las la-wallet"></i></span>
                                <span>{{ __('payout_logs') }}</span>
                            </a>
                        @endif
                        @if (Sentinel::getUser()->user_type == 'merchant_staff')
                            @if (hasPermission('read_logs'))
                                <a href="{{ route('merchant.staff.statements') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                                    <span><i class="icon las la-wallet"></i></span>
                                    <span>{{ __('payout_logs') }}</span>
                                </a>
                            @endif
                        @endif
                        @if (Sentinel::getUser()->user_type == 'merchant')
                            <a href="{{ route('merchant.closing.report') }}"
                                class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-file-download"></i><span>{{ __('closing_report') }}</span></a>
                        @endif
                        @if (@settingHelper('preferences')->where('title', 'create_payment_request')->first()->merchant)
                            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.withdraw.create') : route('merchant.staff.withdraw.create') }}"
                                class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon la la-plus"></i><span>{{ __('request') }}</span></a>
                        @else
                            <button class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon la la-plus"></i><span>{{ __('request') . ' (' . __('service_unavailable') . ')' }}</span></button>
                        @endif
                    </div>
                </div>
                <section class="oftions">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                    <div class="default-list-table table-responsive yajra-dataTable">
                                        {{ $dataTable->table(['class' => 'dt-responsive table'], true) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
@push('script')
    @include('merchant.delete-ajax')
    @include('merchant.withdraw.status-ajax')
@endpush
