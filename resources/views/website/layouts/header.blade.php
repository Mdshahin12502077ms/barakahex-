 <!-- Main Header Start -->
<header class="header">
    <!-- Header Top Bar Start -->
    <div class="header__topbar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="header__toplink">
                        <div class="header__link d-flex align-items-center">
                            <div class="icon">
                                <img src="{{ static_asset('website') }}/images/email.svg" alt="email" />
                            </div>
                            <a href="{{ setting('email_address') }}">{{ setting('email_address') }}</a>
                        </div>
                        <div class="header__link d-flex align-items-center">
                            <div class="icon">
                                <img src="{{  static_asset('website') }}/images/phone.svg" alt="phone" />
                            </div>
                            <a href="{{ setting('phone') }}">0{{ setting('phone') }}</a>
                        </div>
                      	<div class="header__link d-flex align-items-center">
                            <div class="icon">
                                <img src="{{  static_asset('website') }}/images/location.svg" alt="location" />
                            </div>
                            <a>{{ setting('address') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Top Bar End -->
    <div class="main__header">
        <nav class="nav">
            <div class="container">
                <div class="header__wrapper">
                    <div class="header__logo">
                        <a href="{{ route('home') }}">
                            <img class="mx-auto d-block"
                                 src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}"
                        </a>
                    </div>
                    <div class="header__menu">
                        <ul class="main__menu">
                            @if($menu_language && is_array(setting('header_menu')) ? count(setting('header_menu')) : 0 != 0 && setting('header_menu') != [])
                                @foreach($menu_language as $key => $value)
                                    <li class="nl-simple" aria-haspopup="true"><a class="h-link" href="{{ @$value['url']  }}">{{ @$value['label'] }}</a></li>
                                @endforeach
                            @endif
                        </ul>
                        <div class="meta__list d-lg-none text-center mt-20">
                            <div>
                                @if(!\Sentinel::getUser())
                                    <a href="{{ route('merchant.dashboard') }}" class="solid__btn">{{ __('login') }}</a>
                                @endif
                                @if(\Sentinel::check())
                                    @if(\Sentinel::getUser()->user_type == 'staff')
                                        <div class="header__btn">
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('dashboard') }}</a>
                                        </div>
                                    @elseif(\Sentinel::getUser()->user_type == 'merchant')
                                        <div class="header__btn">
                                            <a href="{{ route('merchant.dashboard') }}" class="btn btn-primary">{{ __('dashboard') }}</a>
                                        </div>
                                    @else
                                        <div class="header__btn">
                                            <a href="{{ route('merchant.staff.dashboard') }}" class="btn btn-primary">{{ __('dashboard') }}</a>
                                        </div>
                                    @endif
                                @else
                                <div class="header__btn mt-10">
                                    <a href="{{ route('register') }}" class="btn btn-primary">{{ __('become_a_merchant') }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="header__meta">
                        <div class="language__dropdown">
                            @php
                                    $active_locale = '';
                                    $languages = app('languages');
                                    $locale_language = $languages->where('locale', app()->getLocale())->first();
                                    if ($locale_language) {
                                        $active_locale = $locale_language->name;
                                    }

                                    $active_locales = Str::lower($active_locale);
                                @endphp
                            <div div class="selected">{{ __($active_locales) }}</div>
                            <ul class="list">
                                @foreach($languages as $language)
                                @php
                                    $name = Str::lower($language->name);
                                @endphp
                                    <li class="language-selector">
                                        <a href="{{ setLanguageRedirect($language->locale) }}">
                                            {{ __($name)}}

                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="meta__list">
                            @if(!\Sentinel::getUser())
                                <a href="{{ route('merchant.dashboard') }}" class="solid__btn">{{ __('login') }}</a>
                            @endif
                            @if(\Sentinel::check())
                                @if(\Sentinel::getUser()->user_type == 'staff')
                                    <div class="header__btn">
                                        <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('dashboard') }}</a>
                                    </div>
                                @elseif(\Sentinel::getUser()->user_type == 'merchant')
                                    <div class="header__btn">
                                        <a href="{{ route('merchant.dashboard') }}" class="btn btn-primary">{{ __('dashboard') }}</a>
                                    </div>
                                @else
                                    <div class="header__btn">
                                        <a href="{{ route('merchant.staff.dashboard') }}" class="btn btn-primary">{{ __('dashboard') }}</a>
                                    </div>
                                @endif
                            @else
                            <div class="header__btn">
                                <a href="{{ route('register') }}" class="btn btn-primary">{{ __('become_a_merchant') }}</a>
                            </div>
                            @endif
                        </div>
                        <!-- Header Toggle Start -->
                        <div class="header__toggle">
                            <!-- <div class="toggle__bar"></div> -->
                            <svg width="24" height="20" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M23.1992 2.00039C23.1992 1.57604 23.0306 1.16908 22.7306 0.869015C22.4305 0.568967 22.0236 0.400391 21.5992 0.400391H2.39922C1.9749 0.400391 1.56786 0.568967 1.26786 0.869015C0.967859 1.16908 0.799219 1.57604 0.799219 2.00039C0.799219 2.42474 0.967859 2.8317 1.26786 3.13177C1.56786 3.43181 1.9749 3.60039 2.39922 3.60039H21.5992C22.0236 3.60039 22.4305 3.43181 22.7306 3.13177C23.0306 2.8317 23.1992 2.42474 23.1992 2.00039ZM23.1992 10.0004C23.1992 9.57604 23.0306 9.16908 22.7306 8.86901C22.4305 8.56897 22.0236 8.40039 21.5992 8.40039H11.9992C11.5749 8.40039 11.1679 8.56897 10.8679 8.86901C10.5679 9.16908 10.3992 9.57604 10.3992 10.0004C10.3992 10.4247 10.5679 10.8318 10.8679 11.1318C11.1679 11.4318 11.5749 11.6004 11.9992 11.6004H21.5992C22.0236 11.6004 22.4305 11.4318 22.7306 11.1318C23.0306 10.8318 23.1992 10.4247 23.1992 10.0004ZM23.1992 18.0004C23.1992 17.5761 23.0306 17.169 22.7306 16.869C22.4305 16.569 22.0236 16.4004 21.5992 16.4004H2.39922C1.9749 16.4004 1.56786 16.569 1.26786 16.869C0.967859 17.169 0.799219 17.5761 0.799219 18.0004C0.799219 18.4247 0.967859 18.8318 1.26786 19.1318C1.56786 19.4318 1.9749 19.6004 2.39922 19.6004H21.5992C22.0236 19.6004 22.4305 19.4318 22.7306 19.1318C23.0306 18.8318 23.1992 18.4247 23.1992 18.0004Z"
                                    fill="#212529"
                                />
                            </svg>
                        </div>
                        <!-- Hrader Toggle End -->
                    </div>
                    <!-- Header Meta End -->
                </div>
            </div>
        </nav>
    </div>
    <!-- Main Header End -->
</header>
