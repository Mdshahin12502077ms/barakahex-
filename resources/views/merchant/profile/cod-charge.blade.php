@extends('backend.layouts.master')

@section('title')
    {{ __('cash_on_delivery_charge') }}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.profile.profile-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <h5>{{ __('cash_on_delivery_charge') }}</h5>
                        </div>
                        <table class="table table-responsive">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('location') }}
                                    </th>
                                    <th>{{ __('charge') }}(%)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cod_charges = Sentinel::getUser()->user_type == 'merchant' ? Sentinel::getUser()->merchant->cod_charges : Sentinel::getUser()->staffMerchant->cod_charges;
                                @endphp
                                @foreach ($cod_charges as $key => $cod_charge)
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
    </section>
@endsection
