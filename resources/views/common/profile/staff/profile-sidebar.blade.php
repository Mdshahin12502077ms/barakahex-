<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-content="userAside"
    data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <div class="user-card">
                <div class="user-avatar bg-primary">
                    <img src="{{ optional(\Sentinel::getUser()->image)->image_small_two ? asset(optional(\Sentinel::getUser()->image)->image_small_two) : getFileLink('80X80', []) }}">
                </div>
                <div class="user-info">
                    <span
                        class="lead-text">{{ \Sentinel::getUser()->first_name . ' ' . \Sentinel::getUser()->last_name }}</span>
                    <span class="sub-text">{{ \Sentinel::getUser()->email }}</span>
                </div>
            </div>
        </div>

        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li><a class="{{ strpos(url()->current(), '/profile') ? 'active' : '' }}"
                        href="{{ route('staff.profile') }}">
                        <span>{{ __('personal_information') }}</span>
                    </a>
                </li>
                @if (!blank(Sentinel::getUser()->accounts(Sentinel::getUser()->id)))
                    <li><a class="{{ strpos(url()->current(), '/user-accounts') ? 'active' : '' }}"
                            href="{{ route('user.accounts') }}">
                            <span>{{ __('accounts') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a class="{{ strpos(url()->current(), '/payment-logs') ? 'active' : '' }}"
                        href="{{ route('staff.payment.logs') }}">
                        <span>{{ __('payout_logs') }}</span>
                    </a>
                </li>
                <li>
                    <a class="{{ strpos(url()->current(), '/account-activity') ? 'active' : '' }}"
                        href="{{ route('staff.account-activity') }}">
                        <span>{{ __('login_activity') }}</span>
                    </a>
                </li>
                <li><a class="{{ strpos(url()->current(), '/security-settings') ? 'active' : '' }}"
                        href="{{ route('staff.security-settings') }}">
                        <span>{{ __('security_settings') }}</span>
                    </a>
                </li>
                <li><a href="javascript:void(0);" onclick="logout_user_devices('/logout-other-devices', '')"
                        id="delete-btn">
                        <span> {{ __('logout_from_other_devices') }} </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@include('common.script')
{{-- Update Profile modal --}}
<div class="modal fade" tabindex="-1" id="update-profile">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('update_profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-lg">
                <form action="{{ route('staff.update.profile') }}" class="form-validate is-alter" method="POST"
                    enctype="multipart/form-data" id="update-profile-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="first_name">{{ __('first_name') }}</label>
                        <input type="text" hidden name="id" id="id"
                            value="{{ \Sentinel::getUser()->id }}">
                        <input type="text" name="first_name" class="form-control" id="first_name"
                            value="{{ \Sentinel::getUser()->first_name }}" placeholder="{{ __('first_name') }}"
                            required>
                    </div>
                    @if ($errors->has('first_name'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('first_name') }}</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label" for="last_name">{{ __('last_name') }}</label>
                        <input type="text" name="last_name" class="form-control"
                            value="{{ \Sentinel::getUser()->last_name }}" id="last_name"
                            placeholder="{{ __('last_name') }}" required>
                    </div>
                    @if ($errors->has('last_name'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('last_name') }}</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('email') }}</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ \Sentinel::getUser()->email }}" id="email" placeholder="{{ __('email') }}"
                            required>
                    </div>
                    @if ($errors->has('email'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('email') }}</p>
                        </div>
                    @endif
                    <div class="mb-3 text-center mt-2">
                        @if (Sentinel::getUser()->image)
                            <img src="{{ Sentinel::getUser()->image->original_image }}" id="img_profile"
                                class="img-thumbnail user-profile">
                        @else
                            <img src="{{ static_asset('admin/images/default/user.jpg') }}" id="img_profile"
                                class="img-thumbnail user-profile">
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="default-06">{{ __('profile_image') }}</label>
                            <input type="file" class="custom-file-input sp_file_input form-control image_pick form-control" data-image-for="profile"
                                id="image" name="image">
                        @if ($errors->has('image'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('image') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3 text-right mt-3">
                            <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('css')
@endpush
