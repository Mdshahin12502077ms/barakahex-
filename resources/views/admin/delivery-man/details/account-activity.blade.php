@extends('backend.layouts.master')

@section('title')
{{__('login_activity')}}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.delivery-man.details.sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <div class="">
                                <div>
                                    <h5>{{__('login_activity')}}</h5>
                                </div>
                                <div>
                                    <p>{{__('here_is_login_activity')}}.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table table-ulogs">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{__('browser')}}</th>
                                                <th>{{__('platform')}}</th>
                                                <th><span class="d-none d-sm-inline-block overline-title">{{__('ip')}}</span></th>
                                                <th class="text-left"><span >{{__('time')}}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($login_activities as $login_activity)
                                            <tr>
                                                <td>{{$login_activity->browser}}</td>
                                                <td><span>{{$login_activity->platform}}</span></td>
                                                <td><span class="d-none d-sm-inline-block">{{$login_activity->ip}}</span></td>
                                                <td class="text-left">{{$login_activity->created_at != ""? date('M d, Y h:i a', strtotime($login_activity->created_at)):''}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex  justify-content-end">
                                    {!! $login_activities->appends(Request::except('page'))->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
