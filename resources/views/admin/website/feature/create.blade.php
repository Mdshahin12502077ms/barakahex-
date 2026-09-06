
@extends('backend.layouts.master')
@section('title', __('feature'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('add_new_feature') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                        <form action="{{ route('features.store') }}" method="POST" class="form" enctype="multipart/form-data">@csrf
                            <div class="row gx-20 add-coupon">
                                <input type="hidden" value="{{ $lang }}" name="lang">
                                <input type="hidden" class="is_modal" value="0"/>
                                <div class="col-lg-6 input_file_div mb-4">
                                    <div class="mb-3">
                                        <label class="form-label mb-1">{{__('icon') }}</label>
                                        <label for="image" class="file-upload-text">
                                            <p></p>
                                            <span class="file-btn">{{__('choose_file') }}</span>
                                        </label>
                                        <input class="d-none file_picker" type="file" id="image"
                                               name="feature_icon">
                                        <div class="nk-block-des text-danger">
                                            <p class="feature_icon_error error">{{ $errors->first('feature_icon') }}</p>
                                        </div>
                                    </div>
                                    <div class="selected-files d-flex flex-wrap gap-20">
                                        <div class="selected-files-item">
                                            <img class="selected-img" src="{{ getFileLink('80X80',[]) }}"
                                                 alt="favicon">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">{{ __('title') }}</label>
                                        <input type="text" class="form-control rounded-2 " id="title" name="title">
                                        <div class="nk-block-des text-danger">
                                            <p class="title_error error"></p>
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
    </section>
    @include('admin.website.component.new_menu')
@endsection
