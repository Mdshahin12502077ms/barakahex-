@extends('backend.layouts.master')

@section('title')
{{__('personal_information')}}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.delivery-man.details.sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                            <div class="d-flex justify-content-between align-items-center mb-12">
                                <div>
                                    <h5>{{__('personal_information')}}</h5>
                                    <div class="nk-block-des">
                                        <p>{{__('personal_info_message')}}</p>
                                    </div>
                                </div>
                                <div class="oftions-content-right">
                                    @if(hasPermission('deliveryman_update'))
                                        <a href="{{route('delivery.man.edit', $delivery_man->id)}}"  class="btn sg-btn-primary align-items-center gap-1 d-md-inline-flex"><i class="la la-edit"></i><span>{{__('edit')}}</span></a>

                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="text-nowrap table-responsive">
                                        <table class="table">
                                            <tbody>

                                                <tr data-bs-toggle="modal" data-bs-target="#profile-edit">
                                                    <td>{{__('full_name')}}</td>
                                                    <td>{{$delivery_man->user->first_name.' '.$delivery_man->user->last_name}}</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('email')}}</td>
                                                    <td>{{$delivery_man->user->email}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('phone')}}</td>
                                                    <td>{{$delivery_man->phone_number}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('driving_license')}}</td>
                                                    <td>
                                                        @if(!blank($delivery_man->driving_license))
                                                            <a href="{{ getFileLink('80X80', $delivery_man->driving_license) }}" target="_blank"> <i class="icon  las la-external-link-alt"></i> {{ __('driving_license') }}</a>
                                                        @else
                                                            {{ __('not_available') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('current_amount')}}</td>
                                                    <td>{{ format_price($delivery_man->balance($delivery_man->id)) }}</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('status')}}</td>
                                                    <td>
                                                        @if($delivery_man->user->status == \App\Enums\StatusEnum::INACTIVE)
                                                            <span class="tb-status text-info">{{__('inactive')}}</span>
                                                        @elseif($delivery_man->user->status == \App\Enums\StatusEnum::ACTIVE)
                                                            <span class="tb-status text-success">{{__('active')}}</span>
                                                        @else
                                                            <span class="tb-status text-danger">{{__('suspend')}}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('last_login')}}</td>
                                                    <td>{{$delivery_man->user->last_login != ""? date('M y, Y h:i a', strtotime($delivery_man->user->last_login)):''}}</td>
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

