@extends('backend.layouts.master')

@section('title')
    {{ __('shops') . ' ' . __('list') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-lg-10 col-md-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <div>
                        <div>
                            <h3 class="section-title">{{ __('lists') }}</h3>
                        </div>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $shop->count() }} {{ __('shops') }}.</p>
                        </div>
                    </div>
                    <div>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add-shop"
                            class="btn sg-btn-primary d-md-inline-flex align-items-center justify-content-center gap-1"><i
                                class="las la-plus"></i><span>{{ __('add') }}</span></a>
                        <div class="oftions-content-right align-self-start d-lg-none">
                            <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                data-bs-target="userAside"><i class="las la-plus"></i></a>
                        </div>
                    </div>
                </div>
                <section class="oftions">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <section class="oftions">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12">
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
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>
    @include('merchant.profile.modals')
@endsection
@include('merchant.delete-ajax')
@include('merchant.profile.default-shop-ajax')




