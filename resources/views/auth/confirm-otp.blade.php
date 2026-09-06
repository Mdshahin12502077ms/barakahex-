@extends('auth.master')

@section('title')
    {{__('merchant_registration')}}
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
                      <img class="mx-auto d-block mb-3"
                           src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : getFileLink('80X80', []) }}" alt="logo">
                  </a>
                <h3>{{__('enter_otp_to_complete_registration')}}</h3>

                <form method="POST" action="{{ route('confirm-otp') }}">
                    @csrf
                    <input type="text" name="id" hidden class="form-control form-control-lg" value="{{ $id['temp_id'] }}" id="id">
                    <div class="mb-4">
                        <div class="form-label-group">
                            <label class="form-label" for="otp">{{ __('otp') }}</label>
                        </div>
                        <input type="text" name="otp" class="form-control form-control-lg" id="otp" placeholder="{{__('*****')}}"
                               required>
                        @if($errors->has('otp'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('otp') }}</p>
                            </div>
                        @endif
                    </div>
                        <div class="mb-20"><button type="submit" class="btn btn-lg sg-btn-primary d-block w-100">{{__('submit')}}</button></div>
                    </form>
              </div>
          </div>
        </div>
      </div>
    </section>

    @if(Session::has('success'))
        {!! Toastr::success(Session::get("success")) !!}
    @elseif(Session::has('danger'))
        {!! Toastr::error(Session::get("danger")) !!}
    @endif
@endsection
