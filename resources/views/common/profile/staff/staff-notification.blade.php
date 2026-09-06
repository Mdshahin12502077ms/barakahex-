@extends('backend.layouts.master')

@section('title')
{{__('profile')}}
@endsection

@section('mainContent')
    <div class="container-fluid">
            <div class="card-aside-wrap">
                <div class="card-inner card-inner-lg">
                    <div class="header-top d-flex justify-content-between align-items-center mb-12">
                        <div class="bg-white redious-border p-20 p-sm-30">
                            <h4 class="section-title">Notification Settings</h4>
                            <div class="oftions-content-right">
                                <p>You will get only notification what have enabled.</p>
                            </div>
                            <div class="oftions-content-right align-self-start d-lg-none">
                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><i class="icon las la-bars"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <div class="oftions-content-right">
                            <h6>Security Alerts</h6>
                            <p>You will get only those email notification what you want.</p>
                        </div>
                    </div>
                    <div class="">
                        <div class="gy-3">
                            <div class="g-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" checked id="unusual-activity">
                                    <label class="custom-control-label" for="unusual-activity">Email me whenever encounter unusual activity</label>
                                </div>
                            </div>
                            <div class="g-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="new-browser">
                                    <label class="custom-control-label" for="new-browser">Email me if new browser is used to sign in</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <div class="oftions-content-right">
                            <h6>News</h6>
                            <p>You will get only those email notification what you want.</p>
                        </div>
                    </div>
                    <div class="">
                        <div class="gy-3">
                            <div class="g-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" checked id="latest-sale">
                                    <label class="custom-control-label" for="latest-sale">Notify me by email about sales and latest news</label>
                                </div>
                            </div>
                            <div class="g-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="feature-update">
                                    <label class="custom-control-label" for="feature-update">Email me about new features and updates</label>
                                </div>
                            </div>
                            <div class="g-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" checked id="account-tips">
                                    <label class="custom-control-label" for="account-tips">Email me about tips on using account</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('common.profile.staff.profile-sidebar')

            </div>
    </div>
@endsection
