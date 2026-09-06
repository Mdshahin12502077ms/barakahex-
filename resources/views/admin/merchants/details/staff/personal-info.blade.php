@extends('backend.layouts.master')

@section('title')
{{__('personal_information')}}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.staff.sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                                <h5>{{__('personal_information')}}</h5>
                                <a href="{{route('detail.merchant.staff.edit', $staff->id)}}"  class="btn sg-btn-primary align-items-center gap-1 d-md-inline-flex"><i class="la la-edit"></i> <span>{{__('edit')}}</span></a>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr data-bs-toggle="modal" data-bs-target="#profile-edit">
                                                <td>{{__('full_name')}}</td>
                                                <td>{{$staff->first_name.' '.$staff->last_name}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('email')}}</td>
                                                <td>{{$staff->email}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('phone')}}</td>
                                                <td>{{$staff->phone_number}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('status')}}</td>
                                                <td>
                                                    @if($staff->status == \App\Enums\StatusEnum::INACTIVE)
                                                        <span class="tb-status text-info">{{__('inactive')}}</span>
                                                    @elseif($staff->status == \App\Enums\StatusEnum::ACTIVE)
                                                        <span class="tb-status text-success">{{__('active')}}</span>
                                                    @else
                                                        <span class="tb-status text-danger">{{__('suspend')}}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('last_login')}}</td>
                                                <td>{{$staff->last_login != ""? date('M y, Y h:i a', strtotime($staff->last_login)):''}}</td>
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
