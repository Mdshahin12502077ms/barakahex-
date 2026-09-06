@extends('backend.layouts.master')
@section('title')
    {{ __('warehouse') . ' ' . __('lists') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('warehouse') }}</h3>
                    <div class="oftions-content-right pb-12">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_warehouse"
                           class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon la la-plus"></i><span>{{ __('add_warehouse') }}</span></a>
                    </div>
                </div>
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
        </div>
    </div>
    @include('merchant.profile.modals')
@endsection
@include('merchant.delete-ajax')

