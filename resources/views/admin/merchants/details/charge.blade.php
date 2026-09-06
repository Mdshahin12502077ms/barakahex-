@extends('backend.layouts.master')
@section('title')
    {{ __('delivery_charge') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div
                        class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{ __('delivery_charge') }}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr class="delivery-charge">
                                                <th><span>{{ __('weight') }}</span></th>
                                                <th><span>{{ __('same_day') }}</span></th>
                                                <!-- <th><span>{{ __('next_day') }}</span></th> -->
                                                <th><span>{{ __('sub_city') }}</span></th>
                                                <th><span>{{ __('sub_urban_area') }}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($merchant->charges as $weight => $charge)
                                                <tr class="delivery-charge">
                                                    <td><span class="text-capitalize">{{ $weight }}</span></td>
                                                    <td><span>{{ data_get($charge, 'same_day', 0.0) }} </span></td>
                                                    <!-- <td><span>{{ data_get($charge, 'next_day', 0.0) }} </span></td> -->
                                                    <td><span>{{ data_get($charge, 'sub_city', 0.0) }} </span></td>
                                                    <td><span>{{ data_get($charge, 'sub_urban_area', 0.0) }} </span></td>
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