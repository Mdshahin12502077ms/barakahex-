@extends('backend.layouts.master')
@section('title')
    {{ __('login_activity') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                <h5>{{ __('login_activity') }}</h5>
                                <p>{{ __('here_is_login_activity') }}.
                                </p>
                            </div>
                            <div class="bg-white redious-border p-20 p-sm-30">
                                <table class="table table-ulogs">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('browser') }}</th>
                                            <th>{{ __('platform') }}</th>
                                            <th>{{ __('ip') }}</th>
                                            <th>{{ __('time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($login_activities as $login_activity)
                                            <tr>
                                                <td>{{ $login_activity->browser }}</td>
                                                <td>{{ $login_activity->platform }}</td>
                                                <td><span>{{ $login_activity->ip }}</span></td>
                                                <td><span>{{ $login_activity->created_at != '' ? date('M d, Y h:i a', strtotime($login_activity->created_at)) : '' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex  justify-content-end">
                                    {!! $login_activities->appends(Request::except('page'))->links() !!}
                                </div>
                            </div>
                        </div>
                        @include('admin.users.details.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
