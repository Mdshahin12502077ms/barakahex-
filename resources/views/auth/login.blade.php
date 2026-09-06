@extends('auth.master')

@section('title')
    {{__('login')}}
@endsection
@section('mainContent')
    <section class="login-section">
      <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
          <div class="col-lg-5 col-md-8 col-sm-10 position-relative">
              <img src="{{ static_asset('admin/assets/img/shape/rect.svg')}}" alt="Rect Shape" class="bg-rect-shape">
              <img src="{{ static_asset('admin/assets/img/shape/circle.svg')}}" alt="Rect Shape" class="bg-circle-shape">
              <img src="{{ static_asset('admin/assets/img/shape/circle-block.svg')}}" alt="Rect Shape" class="bg-circle-block-shape">

              <div class="login-form bg-white rounded-20 mt-4 mb-4">
                  <a href="{{ url('/') }}" class="logo-link">
                      <img class="mx-auto d-block mb-3" style="max-height: 42px;"
                           src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}"
                           alt="Logo">
                  </a>

                <h3>{{ __('login_to_your_account') }}</h3>

                <form class="ajax_form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <div class="form-label-group">
                            <label class="form-label" for="email">{{ __('email') }} <span class="text-danger">*</span></label>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" id="email"
                               placeholder="{{__('enter_your_email')}}" required>
                        @if($errors->has('email'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('email') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="password">{{ __('password') }} <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <input type="password" name="password" class="form-control" id="password"
                                       placeholder="{{__('enter_your_password')}}" required>

                                @if($errors->has('password'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('password') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
                        <div class="mt-4">
                          <div id="html_element" class="g-recaptcha" data-sitekey="{{setting('recaptcha_site_key')}}"></div>
                        </div>
                        @endif
                          <div class="custom-checkbox mb-20 mt-3">
                            <label>
                                <input type="checkbox" value="" checked name="remember_me">
                                <span>{{__('remember_me')}}</span>
                            </label>
                          </div>
                        <div class="mb-20"><button type="submit" class="btn btn-lg sg-btn-primary d-block w-100">{{__('login')}}</button></div>
                        <span class="text-center d-block"><a href="{{route('forgot-password')}}" class="sg-text-primary">{{ __('forgot_password') }}?</a></span>
                    </form>
                  <div class="form-note-s2 pt-3 text-center"> {{ __('new_on_our_platform') }} <a
                          href="{{ route('register') }}">{{ __('sign_up_here') }}</a>
                  </div>

                  @if(isDemoMode())
                    <div class="login-as mt-3">
                      <h6>{{ __('login_as') }}</h6>
                      <ul class="login-BTN ">
                        <li>
                          <a href="javascript:void(0)" class="input_filler template-btn bordered-btn-secondary" data-type="admin">{{ __('admin') }}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="input_filler template-btn bordered-btn-secondary" data-type="finance">{{ __('account_manager') }}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="input_filler template-btn bordered-btn-secondary" data-type="branch_manager">{{ __('branch_manager') }}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="input_filler template-btn bordered-btn-secondary" data-type="merchant">{{ __('merchant') }}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="input_filler template-btn bordered-btn-secondary" data-type="merchant_staff">{{ __('merchant_staff') }}</a>
                        </li>
                      </ul>
                    </div>
                  @endif
              </div>
          </div>
        </div>
      </div>
    </section>
@endsection
@push('script')
    <!--====== ReCAPTCHA ======-->
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

    @if ($errors->any())
    <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
    </script>
    @endif
    @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
    <script type="text/javascript">
        var onloadCallback = function() {
        grecaptcha.render('html_element', {
            'sitekey' : '{{setting('recaptcha_site_key')}}',
            'size' : 'md'
        });
        };
    </script>
    @endif
    <script>
        $(document).ready(function () {
            $(document).on('click', '.login-as a', function () {
                var type = $(this).data('type');
                if (type == 'admin') {
                $('#email').val('admin@spagreen.net');
                $('#password').val('123456');
                } else if (type == 'branch_manager') {
                $('#email').val('staff_3@spagreen.net');
                $('#password').val('123456');
                }else if (type == 'finance') {
                $('#email').val('staff_12@spagreen.net');
                $('#password').val('123456');
                }else if (type == 'merchant') {
                $('#email').val('merchant@spagreen.net');
                $('#password').val('123456');
                }else if (type == 'merchant_staff') {
                $('#email').val('mehedi@spagreen.net');
                $('#password').val('123456');
                }
                //('.ajax_form').submit();
            });
        });
    </script>
@endpush
