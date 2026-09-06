@extends('backend.layouts.master')

@section('title')
    {{ __('profile') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class=" d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{__('company_information')}}</h5>
                                <div>
                                    <p>{{__('company_info_message')}}</p>
                                </div>
                            </div>
                            <div>
                                @if(hasPermission('merchant_update'))
                                    <a href="{{ route('merchant.edit', $merchant->id) }}"  class="btn sg-btn-primary align-items-center gap-1 d-md-inline-flex"><i class="las la-edit"></i><span>{{__('edit')}}</span></a>

                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <tbody class="nk-data data-list">

                                            <tr data-bs-toggle="modal" data-bs-target="#profile-edit">
                                                <td>{{ __('company_name') }}</span>
                                                <td>{{ $merchant->company }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('phone') }}</td>
                                                <td>{{ $merchant->phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('account') }}</td>
                                                <td>
                                                    @if ($merchant->status == \App\Enums\StatusEnum::INACTIVE)
                                                        <span class="tb-status text-info">{{ __('inactive') }}</span>
                                                    @elseif($merchant->status == \App\Enums\StatusEnum::ACTIVE)
                                                        <span class="tb-status text-success">{{ __('active') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('registration_status') }}</td>
                                                <td>
                                                    @if ($merchant->registration_confirmed == 0)
                                                        <span class="tb-status text-info">{{ __('not_confirmed') }}</span>
                                                    @elseif($merchant->status == 1)
                                                        <span class="tb-status text-success">{{ __('confirmed') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('website') }}</td>
                                                <td>
                                                    <a href="{{ $merchant->website }}">
                                                        <span class="tb-status text-success">{{ $merchant->website ?? __('not_available') }}</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('trade_license') }}</td>
                                                <td>
                                                    <a href="{{ getFileLink('80X80', $merchant->trade_license) }}">
                                                        <span class="tb-status text-success">{{ getFileLink('80X80', $merchant->trade_license) }}
                                                            @php
                                                                if ($merchant->trade_license != '') {
                                                                    echo '<i class="icon  las la-external-link-alt"></i>';
                                                                }
                                                            @endphp
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('nid') }}</td>
                                                <td>
                                                    <a href="{{ getFileLink('80X80', $merchant->nid) }}">
                                                        <span class="tb-status text-info">{{ $merchant['nid'] ? getFileLink('80X80', $merchant->nid) : __('not_available') }}
                                                            @php
                                                                if ($merchant['nid'] != '') {
                                                                    echo '<i class="icon  las la-external-link-alt"></i>';
                                                                }
                                                            @endphp
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
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
