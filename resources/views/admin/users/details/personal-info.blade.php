@extends('backend.layouts.master')
@section('title')
    {{ __('personal_information') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                <div class="oftions-content-right">
                                    <h4 class="nk-block-title">{{ __('personal_informations') }}</h4>
                                    <div class="nk-block-des">
                                        <p>{{ __('personal_info_message') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>{{ __('full_name') }}</td>
                                            <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ __('email') }}</td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('status') }}</td>
                                            <td>
                                                @if ($user->status == \App\Enums\StatusEnum::INACTIVE)
                                                    <span class="tb-status text-info">{{ __('inactive') }}</span>
                                                @elseif($user->status == \App\Enums\StatusEnum::ACTIVE)
                                                    <span class="tb-status text-success">{{ __('active') }}</span>
                                                @else
                                                    <span class="tb-status text-danger">{{ __('suspend') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('last_login') }}</span>
                                            <td>{{ $user->last_login != '' ? date('M y, Y h:i a', strtotime($user->last_login)) : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('branch') }}</td>
                                            <td>
                                                {{ @$user->branch ? $user->branch->name . ' (' . $user->branch->address . ')' : '' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @include('admin.users.details.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
