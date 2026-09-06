@extends('auth.master')

@section('title')
    {{__('forgot_password')}}
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
                  <div class="mb-3">
                    <h3 class="mb-auto">{{__('forgot_password')}}</h3>
                    <p>{{__('enter_your_email_address_to_recover_your_password') }}</p>
                  </div>

                <form method="POST" action="{{ route('forgot-password') }}">
                    @csrf
                    <div class="mb-4">
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
                        <div class="mb-20"><button type="submit" class="btn btn-lg sg-btn-primary d-block w-100">{{__('submit')}}</button></div>
                        <span class="text-center d-block"><a href="{{route('login')}}" class="sg-text-primary">{{ __('sign_in_instead') }}</a></span>
                    </form>
                  <div class="form-note-s2 pt-3 text-center"> {{ __('new_on_our_platform') }} <a
                          href="{{ route('register') }}">{{ __('sign_up_here') }}</a>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </section>
@endsection
