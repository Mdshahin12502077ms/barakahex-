@extends('backend.layouts.master')

@section('title')
{{__('cash_on_delivery_charge')}}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{ __('cash_on_delivery_charge') }}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><span >{{__('location')}}</th>
                                                <th><span >{{__('charge')}}(%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($merchant->cod_charges as $key => $cod_charge)
                                                <tr class="charge">
                                                    <td>{{__($key)}}</td>
                                                    <td>{{ $cod_charge }}</td>
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
        </div>
    </section>
@endsection
