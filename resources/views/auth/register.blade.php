@extends('auth.master')

@section('title')
    {{ __('merchant_registration') }}
@endsection

@section('mainContent')
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-6 col-md-8 col-sm-12 position-relative">
                    <img src="{{ static_asset('admin/assets/img/shape/rect.svg') }}" alt="Rect Shape" class="bg-rect-shape">
                    <img src="{{ static_asset('admin/assets/img/shape/circle.svg') }}" alt="Rect Shape"
                        class="bg-circle-shape">
                    <img src="{{ static_asset('admin/assets/img/shape/circle-block.svg') }}" alt="Rect Shape"
                        class="bg-circle-block-shape">

                    <div class="login-form bg-white rounded-20 mt-4 mb-4">
                        <a href="{{ url('/') }}" class="logo-link">
                            <img class="mx-auto d-block mb-3" style="max-height: 42px;"
                                src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}"
                                alt="Logo">
                        </a>
                        <h3>{{ __('merchant_registration') }}</h3>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <div class="form-label-group">
                                    <label class="form-label" for="company">{{ __('company_name') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <input type="text" name="company" value="{{ old('company') }}"
                                    class="form-control @error('company') is-invalid @enderror" id="company"
                                    placeholder="{{ __('enter_your_company_name') }}">
                                @if ($errors->has('company'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('company') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-3">
                                    <div class="form-label-group">
                                        <label class="form-label" for="first_name">{{ __('first_name') }} <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                                        class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                                        placeholder="{{ __('enter_your_first_name') }}">
                                    @if ($errors->has('first_name'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('first_name') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-label-group">
                                        <label class="form-label" for="last_name">{{ __('last_name') }}</label>
                                    </div>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control"
                                        placeholder="{{ __('enter_your_last_name') }}">
                                    @if ($errors->has('last_name'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('last_name') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-label-group">
                                    <label class="form-label" for="address">{{ __('address') }}</label>
                                </div>
                                <textarea class="form-control" id="address" name="address"
                                    placeholder="{{ __('enter_your_address') }}">{{ old('address') }}</textarea>
                                @if ($errors->has('address'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('address') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="form-label-group">
                                    <label class="form-label" for="phone_number">{{ __('phone') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                    class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                    placeholder="{{ __('enter_your_phone') }}">
                                @if ($errors->has('phone_number'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('phone_number') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="form-label-group">
                                    <label class="form-label" for="email">{{ __('email') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    placeholder="{{ __('enter_your_email') }}">
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('email') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-label-group">
                                <label class="form-label" for="password">{{ __('password') }} <span
                                        class="text-danger">*</span></label>
                            </div>
                            <div class="form-control-wrap mb-3">

                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                    placeholder="{{ __('enter_your_password') }}">

                                @if ($errors->has('password'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('password') }}</p>
                                    </div>
                                @endif
                            </div>
                            @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
                                <div class="row ml-1 mb-1 mt-3">
                                    <div id="html_element" class="g-recaptcha" data-sitekey="{{setting('recaptcha_site_key')}}">
                                    </div>
                                    @if ($errors->has('g-recaptcha-response'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('g-recaptcha-response') }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="custom-checkbox mb-20">
                                <label>
                                    <input type="checkbox" value="" checked="" name="agree_with" id="checkbox_agree">
                                    <span>
                                        {{ __('i_agree') }}
                                        <a tabindex="-1" target="_blank"
                                            href="{{ url('/privacy-policy') }}">{{ __('privacy_policy') }}</a> &amp;
                                        <a tabindex="-1" target="_blank" href="{{ url('/terms-condition') }}">
                                            {{ __('terms') }}</a>
                                    </span>
                                </label>
                            </div>
                            <div class="mb-20"><button type="submit"
                                    class="btn btn-lg sg-btn-primary d-block w-100">{{ __('register') }}</button></div>
                            <span class="text-center d-block">{{ __('already_have_an_account') }}
                                <a href="{{ route('login') }}">{{ __('sign_in_instead') }}</a>
                            </span>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @push('script')
        <!--====== ReCAPTCHA ======-->
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

        @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
            <script type="text/javascript">
                var onloadCallback = function () {
                    grecaptcha.render('html_element', {
                        'sitekey': '{{setting('recaptcha_site_key')}}',
                        'size': 'md'
                    });
                };
            </script>
        @endif
    @endpush
@endsection