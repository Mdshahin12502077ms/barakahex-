@extends('backend.layouts.master')

@section('title')
    {{ __('otp_logs') }} {{ __('lists') }}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center mb-12">
                        <h3 class="section-title">{{ __('otp_logs') }} {{ __('lists') }}</h3>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="default-list-table table-responsive yajra-dataTable">
                                    {{ $dataTable->table(['class' => 'table table-bordered table-striped align-middle mb-0 w-100'], true) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    @include('common.delete-ajax')
@endpush
