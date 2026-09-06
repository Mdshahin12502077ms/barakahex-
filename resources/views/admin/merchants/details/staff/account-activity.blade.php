@extends('backend.layouts.master')

@section('title')
{{__('login_activity')}}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.staff.sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                                <h5>{{__('login_activity')}}</h5>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table table-ulogs">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><span>{{__('browser')}}</th>
                                                <th><span>{{__('platform')}}</span></th>
                                                <th><span class="d-none d-sm-inline-block">{{__('ip')}}</span></th>
                                                <th><span>{{__('time')}}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($login_activities as $login_activity)
                                                <tr>
                                                    <td>{{$login_activity->browser}}</td>
                                                    <td><span>{{$login_activity->platform}}</span></td>
                                                    <td><span class="d-none d-sm-inline-block">{{$login_activity->ip}}</span></td>
                                                    <td>{{$login_activity->created_at != ""? date('M d, Y h:i a', strtotime($login_activity->created_at)):''}}</td>
                                                </tr>
                                            @endforeach
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
