@extends('auth.master')

@section('title')
    {{__('reset_password')}}
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
                <h3>{{ __('reset_password') }}</h3>

                <form action="{{ route('reset-password', [$email, $resetCode]) }}" class="form-validate" method="post">
                    @csrf
                    <div class="mb-3">
                        <div class="form-label-group">
                            <label class="form-label" for="default-01">{{ __('password') }}</label>
                        </div>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="default-01"
                               placeholder="{{ __('password') }}" required>
                        @if($errors->has('password'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('password') }}</p>
                            </div>
                        @endif

                    </div>
                    <div class="mb-3">
                        <div class="form-label-group">
                            <label class="form-label" for="default-01">{{ __('confirm_password') }}</label>
                        </div>
                        <input type="password" name="password_confirmation" class="form-control  @error('confirm_password') is-invalid @enderror" id="default-01"
                               placeholder="{{ __('confirm_password') }}" required>
                        @if($errors->has('password_confirmation'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('password_confirmation') }}</p>
                            </div>
                        @endif

                    </div>
                    <div class="mb-3">
                        <div class="form-label-group">
                            <label class="form-label" for="password"></label>
                            <a class="" href="{{route('login')}}">{{ __('login') }}?</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="mb-20"><button type="submit" class="btn btn-lg sg-btn-primary d-block w-100">{{__('login')}}</button></div>
                    </div>
                </form>
                  <div class="form-note-s2 pt-4"> {{ __('new_on_our_platform') }} <a
                          href="{{ route('register') }}">{{ __('sign_up_here') }}</a>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </section>
@endsection
