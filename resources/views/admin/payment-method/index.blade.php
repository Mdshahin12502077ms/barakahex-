
@extends('backend.layouts.master')

@section('title')
    {{__('payout_method').' '.__('lists')}}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="section-title">{{__('payout_method')}} {{__('lists')}}</h3>
                        <p>{{__('you_have_total')}} {{ !blank($payments)? $payments->total():'0' }} {{__('payout_method')}}.</p>
                    </div>
                    <div class="oftions-content-right mb-12">
                        @if(hasPermission('payment_method_create'))
                            <a href="{{route('admin.payment.method.create')}}"
                            class="d-flex align-items-center btn sg-btn-primary gap-2">
                                <i class="las la-plus"></i>
                                <span>{{__('add') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
                <section class="oftions">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="default-list-table table-responsive yajra-dataTable">
                                                {{ $dataTable->table(['class' => 'dt-responsive table'], true) }}
                                            </div>
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
@endsection
@push('script')
@include('common.delete-ajax')
@include('common.change-status-ajax')
@endpush


