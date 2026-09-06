@extends('backend.layouts.master')

@section('title')
    {{ __('login_activity') }}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('merchant.staffs.details.sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border activeItem-bd-none p-20 p-lg-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <h4 >{{ __('login_activity') }}</h4>
                        </div>
                        <div class="">
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
                                            <td><span class="sub-text">{{ $login_activity->platform }}</span></td>
                                            <td><span class="d-none d-sm-inline-block">{{ $login_activity->ip }}</span>
                                            </td>
                                            <td>
                                                {{ $login_activity->created_at != '' ? date('M d, Y h:i a', strtotime($login_activity->created_at)) : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
