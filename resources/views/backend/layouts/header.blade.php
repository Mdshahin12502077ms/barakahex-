@php
    $user = Sentinel::getUser();
@endphp
<nav class="navbar navbar-top navbar-expand-lg bg-body-tertiary py-20 bg-white sticky-top">
    <div class="container-fluid g-5">
        <span class="sidebar-toggler">
            <span class="icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 6H3" stroke="#7E7F92" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                    <path d="M21 12H3" stroke="#7E7F92" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                    <path d="M18 18H3" stroke="#7E7F92" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </svg>
            </span>
        </span>
        <a class="navbar-brand ms-auto d-none"
            href="{{ ($user && $user->user_type == 'merchant') ? route('merchant.dashboard') : route('dashboard') }}">
            <img src="{{ getFileLink('80X80', setting('admin_mini_logo')) }}" alt="Logo">
        </a>

        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
            aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="las la-ellipsis-v"></span>
        </button>
        <div class="collapse navbar-collapse navbar-content px-lg-20 navbar-respons" id="navbarScroll">
            <div class="navbar-left-content me-lg-auto d-flex align-items-center gap-20">

                <ul class="dashboard-btn d-flex align-items-center gap-lg-20 gap-sm-2">
                    <li>
                        <a href="{{ route('clear.cache') }}"
                            class="d-flex align-items-center button-default default-circle-btn gap-2">
                            <i class="las la-hdd"></i>
                            <span>{{ __('clear_cache') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="navbar-right-content">
                <ul class="d-flex align-items-center gap-lg-4 gap-sm-2">

                    <li>
                        @if ($user && @settingHelper('preferences')->where('title', 'create_parcel')->first()->merchant && ($user->user_type == 'merchant' || $user->user_type == 'merchant_staff'))
                            <a href="{{ $user->user_type == 'merchant' ? route('merchant.parcel.create') : route('merchant.staff.parcel.create') }}"
                                class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon la la-plus"></i><span>{{ __('create_parcel') }}</span></a>
                        @endif
                    </li>

                    <li class="visit-website">
                        <a href="{{ route('home') }}" target="_blank">
                            <i class="las la-globe-americas"></i>
                            <span class="icon-hover">{{ __('visit_website') }}</span>
                        </a>
                    </li>

                    <li class="visit-website dropdown notification">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="las la-bell"></i>
                            <span class="has_notifications">{{ $notificationCount }}</span>
                        </a>
                        @include('backend.layouts.package_subscribe')
                    </li>

                    <li class="select-language dropdown pe-lg-20">
                        @php
                            $active_locale = 'English';
                            $languages = app('languages');
                            $locale_language = $languages->where('locale', app()->getLocale())->first();
                            if ($locale_language) {
                                $active_locale = $locale_language->name;
                            }

                            $active_locales = Str::lower($active_locale);
                        @endphp
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __($active_locales) }}
                        </a>
                        <ul class="dropdown-menu popup-card">
                            @foreach ($languages as $lang)
                                @php
                                    $name = Str::lower($lang->name);
                                @endphp
                                <li>
                                    <a class="dropdown-item" href="{{ setLanguageRedirect($lang->locale) }}">
                                        <img src="{{ static_asset($lang->flag ?: 'admin/img/flag/united-kingdom.svg') }}"
                                            alt="United Kingdom">
                                        {{ __($name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    <li class="dropdown pe-lg-20">
                        @if($user)
                            <a href="#" class="dropdown-toggle d-flex gap-12" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img class="user-avater" src="{{ getFileLink('80X80', $user->image_id) }}"
                                    class="redious-border">
                                <span class="user-name">{{ $user->first_name . ' ' . $user->last_name }}</span>
                                <span class="active_status"></span>
                            </a>
                            <ul class="dropdown-menu popup-card">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ $user->user_type == 'merchant' ? route('merchant.profile') : ($user->user_type == 'merchant_staff' ? route('merchant.staff.profile') : route('staff.profile')) }}">
                                        <i class="la la-user"></i>
                                        <span>{{ __('profile') }}</span>
                                    </a>
                                </li>
                                @if(!blank($user->accounts($user->id)))
                                    <li>
                                        <a href="{{route('user.accounts')}}" class="dropdown-item">
                                            <span class="icon"><i class="las la-heading"></i></span>
                                            <span>{{ __('accounts') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ $user->user_type == 'merchant' ? route('merchant.statements') : ($user->user_type == 'merchant_staff' ? route('merchant.staff.statements') : route('staff.payment.logs')) }}"
                                        class="dropdown-item">
                                        <span class="icon"><i class="icon las la-wallet"></i></span>
                                        <span>{{ __('payout_logs') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ $user->user_type == 'merchant' ? route('merchant.security-settings') : ($user->user_type == 'merchant_staff' ? route('merchant.staff.security-settings') : route('staff.security-settings')) }}">
                                        <i class="la la-user-shield"></i>
                                        <span>{{ __('change_password') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ $user->user_type == 'merchant' ? route('merchant.account-activity') : ($user->user_type == 'merchant_staff' ? route('merchant.staff.account-activity') : route('staff.account-activity')) }}">
                                        <i class="la la-file-alt"></i>
                                        <span>{{ __('login_activity') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ $user->user_type == 'merchant' ? route('merchant.logout') : ($user->user_type == 'merchant_staff' ? route('merchant.staff.logout') : route('logout')) }}">
                                        <i class="la la-sign-out"></i>
                                        <span>{{ __('logout') }}</span>
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>