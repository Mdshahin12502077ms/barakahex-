@extends('backend.layouts.master')

@section('title')
    {{ __('statements') }}
@endsection



@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('common.profile.staff.staff-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{__('transaction')}}</h5>
                            </div>
                        </div>
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
                </div>
            </div>
        </div>
    </section>
@endsection
