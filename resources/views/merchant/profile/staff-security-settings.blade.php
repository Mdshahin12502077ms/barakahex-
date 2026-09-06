@extends('backend.layouts.master')

@section('title')
    {{ __('profile') }}
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
                                <h5>{{ __('change_password') }}</h5>
                                <p>{{ __('change_password_message') }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mt-3">
                                <form
                                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.change-password') : route('merchant.staff.change-password') }}"
                                    class="form-validate" method="POST" enctype="multipart/form-data" id="change-password-form">
                                    @csrf

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="personal">
                                            <div class="gy-4">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="full-current_password">{{ __('current_password') }}</label>
                                                    <input type="password" name="current_password" class="form-control"
                                                        id="full-current_password" placeholder="{{ __('current_password') }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="full-new_password">{{ __('new_password') }}</label>
                                                    <input type="password" name="new_password" class="form-control"
                                                        id="full-new_password" placeholder="{{ __('new_password') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="full-confirm_new_password">{{ __('confirm_new_password') }}</label>
                                                    <input type="password" name="confirm_new_password" class="form-control"
                                                        id="full-confirm_new_password" placeholder="{{ __('confirm_new_password') }}"
                                                        required>
                                                </div>
                                                <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                    <li>
                                                        <button type="submit" class="btn sg-btn-primary btn-primary">{{ __('update') }}</button>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-bs-dismiss="modal" class="link link-light">Cancel</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div><!-- .tab-pane -->
                                    </div><!-- .tab-content -->
                                </form>
                                @if (\Sentinel::getUser()->last_password_change != '')
                                    <li>
                                        <i class="text-soft text-date fs-12px">{{ __('last_changed') }}:
                                            <span>{{ \Sentinel::getUser()->last_password_change != '' ? date('M d, Y') : '' }}</span></i>
                                    </li>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            $('#change-password-form').on('submit', function() {

                if ($('#full-new_password').val() != $('#full-confirm_new_password').val()) {
                    toastr.clear();
                    toastr.success("{{ __('new_passwords_do_not_match') }}");

                    return false;
                }
            });

        });
    </script>
@endpush
