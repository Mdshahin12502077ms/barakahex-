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
                        <div class="d-flex justify-content-between">
                            <div class="mb-4">
                                <h5 class="">{{ __('login_activity') }}</h5>
                                <p>{{ __('here_is_login_activity') }} </p>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="btn d-flex justify-content-center align-items-center sg-btn-primary resubmit" onclick="logout_user_devices('/logout-other-devices', '')" id="delete-btn">
                                    {{ __('logout_from_all_devices') }}
                                </a>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-start">{{ __('browser') }}</th>
                                    <th class="text-center">{{ __('platform') }}</th>
                                    <th class="text-center">{{ __('ip') }}</th>
                                    <th class="text-end">{{ __('time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($login_activities as $login_activity)
                                    <tr>
                                        <td class="text-start">{{ $login_activity->browser }}</td>
                                        <td class="text-center">{{ $login_activity->platform }}</td>
                                        <td class="text-center">{{ $login_activity->ip }}</td>
                                        <td class="text-end">{{ $login_activity->created_at != '' ? date('M d, Y h:i a', strtotime($login_activity->created_at)) : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            {!! $login_activities->appends(Request::except('page'))->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
