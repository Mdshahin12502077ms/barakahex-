
@extends('backend.layouts.master')
@section('title', __('edit_about'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('edit_about') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <form action="{{ route('abouts.update',$about->id) }}" class="form-validate form"
                                    method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <input type="hidden" name="id" value="{{ $about->id }}">
                                    <input type="hidden" value="{{ $lang }}" name="lang">
                                    <input type="hidden"
                                       value="{{ @$about_language->translation_null == 'not-found' ? '' : @$about_language->id }}"
                                       name="translate_id">

                                    <div class="col-lg-6 input_file_div mb-4">
                                        <div class="mb-3">
                                            <label class="form-label mb-1">{{__('icon') }}</label>
                                            <label for="image" class="file-upload-text">
                                                <p></p>
                                                <span class="file-btn">{{__('choose_file') }}</span>
                                            </label>
                                            <input class="d-none file_picker" type="file" id="image"
                                                   name="about_icon">
                                            <div class="nk-block-des text-danger">
                                                <p class="about_icon_error error">{{ $errors->first('about_icon') }}</p>
                                            </div>
                                        </div>
                                        <div class="selected-files d-flex flex-wrap gap-20">
                                            <div class="selected-files-item">
                                                <img class="selected-img" src="{{ getFileLink('original_image', $about->icon) }}"
                                                     alt="favicon">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="title" class="form-label">{{ __('title') }}</label>
                                            <input type="text" class="form-control rounded-2 " value="{{@$about_language->title}}" id="title" name="title">
                                            <div class="nk-block-des text-danger">
                                                <p class="title_error error"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Success Description -->
                                    <div class="col-lg-12">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between">
                                                <label for="description" class="form-label">{{ __('description') }}</label>
                                                @include('common.ai_btn',[
                                                        'name' => 'ai_short_description',
                                                        'length' => '200',
                                                        'topic' => 'ai_content_name',
                                                        'use_case' => 'short testimonial description for an learning website',
                                                        ])
                                            </div>
                                            <textarea class="form-control ai_short_description" id="description" name="description">{{ __(@$about_language->description)  }}</textarea>
                                            <div class="nk-block-des text-danger">
                                                <p class="description_error error"></p>
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
            </div>
        </div>
    </section>
    @include('admin.website.component.new_menu')
@endsection
