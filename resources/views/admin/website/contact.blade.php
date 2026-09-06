@extends('backend.layouts.master')
@section('title', __('contact_section_content'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('contact_section_content') }}</h3>
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <form action="{{ route('admin.contact.section') }}"  method="POST"  enctype="multipart/form-data">@csrf
                            <div class="row gx-20">
                                <input type="hidden" value="0" class="is_modal" name="is_modal">
                                <input type="hidden" name="site_lang" value="{{$lang}}">
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="contact_title" class="form-label">{{ __('title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="contact_title"
                                                name="contact_title" value="{{ setting('contact_title', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="contact_title_error error">{{ $errors->first('lang') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="contact_subtitle" class="form-label">{{ __('subtitle') }}</label>
                                        <input type="text" class="form-control rounded-2" id="contact_subtitle"
                                                name="contact_subtitle" value="{{ setting('contact_subtitle', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="contact_subtitle_error error">{{ $errors->first('lang') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="contact_btn_label" class="form-label">{{ __('btn_label') }}</label>
                                        <input type="text" class="form-control rounded-2" id="contact_btn_label"
                                                name="contact_btn_label" value="{{ setting('contact_btn_label', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="contact_btn_label_error error">{{ $errors->first('lang') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="contact_map" class="form-label">{{ __('map') }}</label>
                                        <input type="text" class="form-control rounded-2"
                                                name="contact_map" value="{{ setting('contact_map') }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="contact_map_error error">{{ $errors->first('lang') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center mt-30">
                                    <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                                    @include('common.loading-btn',['class' => 'btn sg-btn-primary'])
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


