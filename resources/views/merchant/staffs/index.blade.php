@extends('backend.layouts.master')

@section('title')
    {{ __('staffs') . ' ' . __('lists') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('staffs') }}</h3>
                    <div class="oftions-content-right pb-12">
                        <a href="{{ route('merchant.staff.create') }}"
                            class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon la la-plus"></i><span>{{ __('add') }}</span></a>
                    </div>
                </div>
                <p>{{ __('you_have_total') }} {{ $staffs->total() }} {{ __('staffs') }}.</p>
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
@endsection
@include('merchant.delete-ajax')

