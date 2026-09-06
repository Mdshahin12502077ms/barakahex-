@extends('backend.layouts.master')
@section('title')
    {{ __('profile') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('common.profile.staff.staff-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-12">
                                <div class="">
                                    <h4>{{ __('personal_information') }}</h4>
                                        <div>
                                            <p>{{ __('personal_info_message') }}</p>
                                        </div>
                                </div>
                                <div>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#update-profile"
                                        class="btn sg-btn-primary align-items-center gap-1 d-md-inline-flex"><i
                                            class="la la-edit"></i><span>{{ __('edit') }}</span></a>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>{{ __('full_name') }}</td>
                                    <td>{{ \Sentinel::getUser()->first_name . ' ' . \Sentinel::getUser()->last_name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('email') }}</td>
                                    <td>{{ \Sentinel::getUser()->email }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('status') }}</td>
                                    <td>
                                        @if (\Sentinel::getUser()->status == \App\Enums\StatusEnum::INACTIVE)
                                            <span class="tb-status text-info">{{ __('inactive') }}</span>
                                        @elseif(\Sentinel::getUser()->status == \App\Enums\StatusEnum::ACTIVE)
                                            <span class="tb-status text-success">{{ __('active') }}</span>
                                        @else
                                            <span class="tb-status text-danger">{{ __('suspend') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if (isset(\Sentinel::getUser()->branch))
                                    <tr>
                                        <td>{{ __('branch') }}</td>
                                        <td>{{ \Sentinel::getUser()->branch->name . ' (' . \Sentinel::getUser()->branch->address . ')' }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ __('last_login') }}</td>
                                    <td>{{ \Sentinel::getUser()->last_login != '' ? date('M y, Y h:i a', strtotime(\Sentinel::getUser()->last_login)) : '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
