@extends('backend.layouts.master')
@section('title', __('theme_options'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('theme_options') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                    <form action="{{ route('admin.theme.options') }}" method="POST" class="form" enctype="multipart/form-data">@csrf
                    <input type="hidden" name="site_lang" value="{{ $lang }}">
                    <input type="hidden" name="menu_name" value="header_menu">
                    <div class="row gx-20">
                            <div class="col-lg-12 input_file_div mb-4">
                                <div class="mb-3">
                                    <label class="form-label mb-1">{{__('light_logo')}} (100X36)</label>
                                    <label for="light_logo"
                                            class="file-upload-text">
                                        <p>1 File Choosen</p>
                                        <span class="file-btn">{{__('choose_file') }}</span></label>
                                    <input class="d-none file_picker" type="file" id="light_logo" name="light_logo">
                                    <div class="nk-block-des text-danger">
                                        <p class="light_logo_error error">{{ $errors->first('light_logo') }}</p>
                                    </div>
                                </div>
                                <div class="selected-files d-flex flex-wrap gap-20">
                                    <div class="selected-files-item">
                                        <img class="selected-img" src="{{ setting('light_logo') && @is_file_exists(setting('light_logo')['original_image']) ? get_media(setting('light_logo')['original_image']) : get_media('images/default/logo/logo_light.png') }}" alt="light_logo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 input_file_div mb-4">
                                <div class="mb-3">
                                    <label class="form-label mb-1">{{__('dark_logo') }} (100X36)</label>
                                    <label for="dark_logo"
                                            class="file-upload-text">
                                        <p>1 File Choosen</p>
                                        <span class="file-btn">{{__('choose_file') }}</span></label>
                                    <input class="d-none file_picker" type="file" id="dark_logo" name="dark_logo">
                                    <div class="nk-block-des text-danger">
                                        <p class="dark_logo_error error">{{ $errors->first('dark_logo') }}</p>
                                    </div>
                                </div>
                                <div class="selected-files d-flex flex-wrap gap-20">
                                    <div class="selected-files-item">
                                        <img class="selected-img" src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}" alt="dark_logo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 input_file_div mb-4">
                                <div class="mb-3">
                                    <label class="form-label mb-1">{{__('favicon') }}</label>
                                    <label for="favicon"
                                            class="file-upload-text">
                                            <p>1 File Choosen</p>
                                            <span class="file-btn">{{__('choose_file') }}</span></label>
                                    <input class="d-none file_picker" type="file" id="favicon" name="favicon">
                                    <div class="nk-block-des text-danger">
                                        <p class="favicon_error error">{{ $errors->first('favicon') }}</p>
                                    </div>
                                </div>
                                <div class="selected-files d-flex flex-wrap gap-20">
                                    <div class="selected-files-item">
                                        @php
                                            $icon = setting('favicon');
                                        @endphp
                                        @if($icon)
                                            <img class="selected-img" src="{{ (is_array($icon) && @is_file_exists($icon['image_80X80'])) ? static_asset($icon['image_80X80']) : static_asset('images/default/favicon/favicon-96x96.png') }}" alt="favicon">
                                        @else
                                            <img class="selected-img" src="{{ static_asset('images/default/favicon/favicon-96x96.png') }}" alt="favicon">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-30">
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
