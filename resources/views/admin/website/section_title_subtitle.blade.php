@extends('backend.layouts.master')
@section('title', __('header_menu'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('section_title_subtitle') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                        <form action="{{ route('admin.section_title_subtitle') }}" method="POST" class="form">
                            @csrf
                            <input type="hidden" name="site_lang" value="{{$lang}}">
                            <div class="pageTitle">
                                <h6 class="sub-title">{{__('section_title')}}</h6>
                            </div>
                            <div class="row gx-20 add-coupon">
                                <input type="hidden" class="is_modal" value="0"/>

                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="partner_logo_section_title" class="form-label">{{ __('partner_logo_section_title') }}</label>
                                        <input type="text" class="form-control rounded-2 ai_content_name" id="partner_logo_section_title" name="partner_logo_section_title" value="{{ setting('partner_logo_section_title', $lang) }}"
                                              >
                                        <div class="nk-block-des text-danger">
                                            <p class="partner_logo_section_title_error error"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="about_section_title" class="form-label">{{ __('about_section_title') }}</label>
                                        <input type="text" class="form-control rounded-2 ai_content_name" id="about_section_title" name="about_section_title" value="{{ setting('about_section_title', $lang) }}"
                                               >
                                        <div class="nk-block-des text-danger">
                                            <p class="about_section_title_error error"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="service_section_title" class="form-label">{{ __('service_section_title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="service_section_title" name="service_section_title" value="{{ setting('service_section_title', $lang) }}"
                                               >
                                        <div class="nk-block-des text-danger">
                                            <p class="service_section_title_error error"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="feature_section_title" class="form-label">{{ __('feature_section_title') }}</label>
                                        <input type="text" class="form-control rounded-2 ai_content_name" id="feature_section_title" name="feature_section_title" value="{{ setting('feature_section_title', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="feature_section_title_error error"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="statistic_section_title" class="form-label">{{ __('statistic_section_title') }}</label>
                                        <input type="text" class="form-control rounded-2 ai_content_name" id="statistic_section_title" name="statistic_section_title" value="{{ setting('statistic_section_title', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="statistic_section_title_error error"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="testimonial_section_title" class="form-label">{{ __('testimonial_section_title') }}</label>
                                        <input type="text" class="form-control rounded-2 ai_content_name" id="testimonial_section_title" name="testimonial_section_title" value="{{ setting('testimonial_section_title', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="testimonial_section_title_error error"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pageTitle">
                                <h6 class="sub-title">{{__('section_subtitle')}}</h6>
                            </div>
                            <div class="row gx-20">
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="about_section_subtitle" class="form-label">{{ __('about_section_subtitle') }}</label>
                                        <input type="text" class="form-control rounded-2 ai_content_name" id="about_section_subtitle" name="about_section_subtitle" value="{{ setting('about_section_subtitle', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="story_section_subtitle_error error"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="align-items-center mt-30">
                                    <button type="submit" class="btn sg-btn-primary">{{__('submit') }}</button>
                                    @include('common.loading-btn',['class' => 'btn sg-btn-primary'])
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        @include('admin.website.component.new_menu')
    @endsection
    @push('js_asset')
        <script src="{{ static_asset('admin/js/jquery.nestable.min.js') }}"></script>
    @endpush
    @push('js')
    @endpush
