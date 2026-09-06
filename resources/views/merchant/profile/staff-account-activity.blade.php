
@extends('backend.layouts.master')

@section('title')
{{__('profile')}}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.profile.profile-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{__('login_activity')}}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table">
                                        <thead class="thead-light">
                                        <tr class="shops-profile">
                                            <th><span >#</span></th>
                                            <th><span >{{__('browser')}}</span></th>
                                            <th><span >{{__('platform')}}</span></th>
                                            <th><span >{{__('ip')}}</span></th>
                                            <th><span >{{__('time')}}</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($login_activities as $key => $login_activity)
                                                <tr>
                                                    <td><span class="text-capitalize">{{$key + 1}}</span></td>
                                                    <td>{{ $login_activity->browser }}</td>
                                                    <td>{{ $login_activity->platform }}</td>
                                                    <td>{{ $login_activity->ip }}</td>
                                                    <td>
                                                        {{ $login_activity->created_at != '' ? date('M d, Y h:i a', strtotime($login_activity->created_at)) : '' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex  justify-content-end">
                                {!! $login_activities->appends(Request::except('page'))->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

