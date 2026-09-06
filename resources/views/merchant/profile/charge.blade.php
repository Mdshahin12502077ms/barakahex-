@extends('backend.layouts.master')

@section('title')
    {{ __('delivery_charge') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row justify-content-md-center">
                <div class="col col-lg-8 col-md-9">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <h5>{{ __('cash_on_delivery_charge') }}</h5>
                        </div>
                        <div class="text-nowrap table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('location') }}
                                        </th>
                                        <th>{{ __('charge') }}({{ __(setting('default_currency')) }})
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cod_charges = \Sentinel::getUser()->user_type == 'merchant' ? \Sentinel::getUser()->merchant->cod_charges : Sentinel::getUser()->staffMerchant->cod_charges;
                                    @endphp
                                    @foreach (@$cod_charges as $key => $cod_charge)
                                        <tr class="charge">
                                            <td>{{ __($key) }}</td>
                                            <td>{{ $cod_charge }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-md-center mt-4">
                <div class="col col-lg-8 col-md-9">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <h5>{{ __('delivery_charge') }}</h5>
                        </div>
                        <div class="text-nowrap table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr class="delivery-charge">
                                        <th>{{ __('weight') }} ({{ __(setting('default_weight')) }})
                                        </th>
                                        <th>{{ __('same_day') }} ({{ __('same_city') }} {{ __(setting('default_currency')) }})
                                        </th>
                                        <!-- <th>{{ __('next_day') }} ({{ __('same_city') }} {{ __(setting('default_currency')) }}) -->
                                        </th>
                                        {{-- <th><span class="overline-title">{{__('frozen')}}</span></th> --}}
                                        <th>{{ __('sub_city') }} ({{ __(setting('default_currency')) }})
                                        </th>
                                        <th>{{ __('sub_urban_area') }} ({{ __(setting('default_currency')) }})
                                        </th>
                                        <th><span class="overline-title">{{__('third_party_booking')}} ({{ __(setting('default_currency')) }})</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $charges = Sentinel::getUser()->user_type == 'merchant' ? Sentinel::getUser()->merchant->charges : Sentinel::getUser()->staffMerchant->charges;
                                    @endphp
                                    @foreach ($charges as $weight => $charge)
                                        <tr class="delivery-charge">
                                            <td class="text-center"><span class="text-capitalize">{{ $weight }}</span></td>
                                            <td class="text-center">
                                                <span>{{ data_get($charge, 'same_day', 0.0) }} </span>
                                            </td>
                                            <!-- <td class="text-center">
                                                <span>{{ data_get($charge, 'next_day', 0.0) }} </span>
                                            </td class="text-center"> -->
                                            {{-- <td><span>{{ data_get($charge, 'frozen', 0.00) }} </span></td> --}}
                                            <td class="text-center">
                                                <span>{{ data_get($charge, 'sub_city', 0.0) }} </span>
                                            </td>
                                            <td class="text-center">
                                                <span>{{ data_get($charge, 'sub_urban_area', 0.0) }} </span>
                                            </td>
                                            <td class="text-center"><span>{{ data_get($charge, 'third_party_booking', 0.00) }} </span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
