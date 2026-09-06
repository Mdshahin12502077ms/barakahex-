@extends('backend.layouts.master')
@section('title', __('statistic'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('statistic') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                        <form action="{{ route('admin.statistic') }}" method="POST" class="form" enctype="multipart/form-data">@csrf
                            <input type="hidden" name="site_lang" value="{{$lang}}">
                            <div class="row gx-20">
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">{{ __('counter1_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="title"
                                                name="counter1_title" value="{{ setting('counter1_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="ctatitle_error error">{{ $errors->first('counter1_title') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2">
                                    <div class="mb-4">
                                        <label for="counter1_sub_title" class="form-label">{{ __('counter1_sub_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter1_sub_title"
                                                name="counter1_sub_title" value="{{ setting('counter1_sub_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter1_sub_title_error error">{{ $errors->first('counter1_sub_title') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="counter1_value" class="form-label">{{ __('counter1_value') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter1_value"
                                                name="counter1_value" value="{{ setting('counter1_value',$lang) }}" step="any">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter1_value_error error">{{ $errors->first('counter1_value') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">{{ __('counter2_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="title"
                                                name="counter2_title" value="{{ setting('counter2_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter2_title_error error">{{ $errors->first('counter2_title') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="mb-4">
                                        <label for="counter2_sub_title" class="form-label">{{ __('counter2_sub_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter2_sub_title"
                                                name="counter2_sub_title" value="{{ setting('counter2_sub_title',$lang) }}" >
                                        <div class="nk-block-des text-danger">
                                            <p class="counter2_sub_title_error error">{{ $errors->first('counter2_sub_title') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="counter2_value" class="form-label">{{ __('counter2_value') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter2_value"
                                                name="counter2_value" value="{{ setting('counter2_value',$lang) }}" step="any">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter2_value_error error">{{ $errors->first('counter2_value') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">{{ __('counter3_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="title"
                                                name="counter3_title" value="{{ setting('counter3_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="ctatitle_error error">{{ $errors->first('counter3_title') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="mb-4">
                                        <label for="counter3_sub_title" class="form-label">{{ __('counter3_sub_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter3_sub_title"
                                                name="counter3_sub_title" value="{{ setting('counter3_sub_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter3_sub_title_error error">{{ $errors->first('counter3_sub_title') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="counter3_value" class="form-label">{{ __('counter3_value') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter3_value"
                                                name="counter3_value" value="{{ setting('counter3_value',$lang) }}" step="any">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter3_value_error error">{{ $errors->first('counter3_value') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">{{ __('counter4_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="title"
                                                name="counter4_title" value="{{ setting('counter4_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="ctatitle_error error">{{ $errors->first('counter4_title') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="mb-4">
                                        <label for="counter4_sub_title" class="form-label">{{ __('counter4_sub_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter4_sub_title"
                                                name="counter4_sub_title" value="{{ setting('counter4_sub_title',$lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter4_sub_title_error error">{{ $errors->first('counter4_sub_title') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="mb-4">
                                        <label for="counter4_value" class="form-label">{{ __('counter4_value') }}</label>
                                        <input type="text" class="form-control rounded-2" id="counter4_value"
                                                name="counter4_value" value="{{ setting('counter4_value',$lang) }}" step="any">
                                        <div class="nk-block-des text-danger">
                                            <p class="counter4_value_error error">{{ $errors->first('counter4_value') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center mt-30">
                                    <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                                    @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.website.component.new_menu')
@endsection
