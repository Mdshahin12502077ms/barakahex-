@extends('backend.layouts.master')

@section('title')
    {{ __('personal_information') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{ __('personal_information') }}</h5>
                                <div>
                                    <p>{{ __('personal_info_message') }}</p>
                                </div>
                            </div>
                            <div>
                                @if (hasPermission('merchant_update'))
                                    <a href="{{ route('merchant.edit', $merchant->id) }}"
                                        class="btn sg-btn-primary align-items-center gap-1 d-md-inline-flex"><i
                                            class="las la-edit"></i><span>{{ __('edit') }}</span></a>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>{{ __('full_name') }}</td>
                                                <td>{{ $merchant->user->first_name . ' ' . $merchant->user->last_name }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ __('email') }}</td>
                                                <td>{{ $merchant->user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('phone') }}</td>
                                                <td>{{ $merchant->phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('status') }}</td>
                                                <td>
                                                    @if ($merchant->user->status == \App\Enums\StatusEnum::INACTIVE)
                                                        <span class="tb-status text-info">{{ __('inactive') }}</span>
                                                    @elseif($merchant->user->status == \App\Enums\StatusEnum::ACTIVE)
                                                        <span class="tb-status text-success">{{ __('active') }}</span>
                                                    @else
                                                        <span class="tb-status text-danger">{{ __('suspend') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('last_login') }}</td>
                                                <td>{{ $merchant->user->last_login != '' ? date('M y, Y h:i a', strtotime($merchant->user->last_login)) : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('trade_license') }}</td>
                                                <td>
                                                    <a href="{{ getFileLink('80X80', $merchant->trade_license) }}" target="_blank"> <i
                                                                class="icon  las la-external-link-alt"></i>
                                                            {{ __('trade_license') }}</a>
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
