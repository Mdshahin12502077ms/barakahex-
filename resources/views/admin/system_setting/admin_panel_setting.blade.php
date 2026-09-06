@extends('backend.layouts.master')
@section('title', __('admin_panel_setting'))
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-xxl-8 ">
                <h3 class="section-title">{{ __('admin_panel_setting') }}</h3>
                <div class="bg-white redious-border pt-30 p-20 p-sm-30">
                    <div class="section-top">
                        <h6>{{ __('admin_panel_setting') }}</h6>
                    </div>

                    <form action="{{ route('admin.panel-setting') }}" method="post"  enctype="multipart/form-data">@csrf
                        <input type="hidden" name="r" value="{{ url()->current() }}" class="r">
                        <input type="hidden" name="site_lang" value="{{ $lang }}">
                        <div class="row gx-20">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="mb-4">
                                    <label for="admin_panel_title" class="form-label">{{__('title') }}</label>
                                    <input type="text" class="form-control rounded-2" id="admin_panel_title"
                                           placeholder="{{__('title') }}" name="admin_panel_title" value="{{ old('admin_panel_title', setting('admin_panel_title', $lang) ) }}">
                                    <div class="nk-block-des text-danger">
                                        <p class="admin_panel_title_error error"></p>
                                    </div>
                                </div>
                            </div>
                            <!-- End Title -->

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="mb-4">
                                    <label for="system_short_name" class="form-label">{{ __('system_short_name') }}</label>
                                    <input type="text" class="form-control rounded-2" id="system_short_name"
                                           placeholder="{{__('system_short_name') }}" name="system_short_name" value="{{ old('system_short_name', setting('system_short_name', $lang) ) }}">
                                    <div class="nk-block-des text-danger">
                                        <p class="system_short_name_error error"></p>
                                    </div>
                                </div>
                            </div>
                            <!-- End System Short Name -->

                            <div class="col-12 col-md-6 col-lg-6 input_file_div mb-4">
                                <div class="mb-3">
                                    <label class="form-label mb-1">{{__('logo') }} (100X36)</label>
                                           <input class="form-control sp_file_input file_picker" type="file" id="admin_logo"  name="admin_logo">
                                    <div class="nk-block-des text-danger">
                                        <p class="admin_logo_error error">{{ $errors->first('admin_logo') }}</p>
                                    </div>
                                </div>
                                <div class="selected-files d-flex flex-wrap gap-20">
                                    <div class="selected-files-item">
                                        <img class="selected-img" src="{{ getFileLink('80X80', setting('admin_logo')) }}" alt="admin_logo">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6 input_file_div mb-4">
                                <div class="mb-3">
                                    <label class="form-label mb-1">{{__('mini_logo') }} (1:1)</label>
                                        <input class="form-control sp_file_input file_picker" type="file" id="admin_mini_logo" name="admin_mini_logo">
                                    <div class="nk-block-des text-danger">
                                        <p class="admin_mini_logo_error error">{{ $errors->first('admin_mini_logo') }}</p>
                                    </div>
                                </div>
                                <div class="selected-files d-flex flex-wrap gap-20">
                                    <div class="selected-files-item">
                                        <img class="selected-img" src="{{ getFileLink('80X80',setting('admin_mini_logo')) }}" alt="admin_mini_logo">
                                    </div>
                                </div>
                            </div>

                            @php
                                $icon = setting('admin_favicon');
                           @endphp

                            <div class="col-12 col-md-6 col-lg-6 input_file_div mb-4">
                                <div class="mb-3">
                                    <label class="form-label mb-1">{{__('favicon') }}</label>
                                    <input class="form-control sp_file_input file_picker" type="file" id="admin_favicon" name="admin_favicon">
                                    <div class="nk-block-des text-danger">
                                        <p class="admin_favicon_error error">{{ $errors->first('admin_favicon') }}</p>
                                    </div>
                                </div>
                                <div class="selected-files d-flex flex-wrap gap-20">
                                    <div class="selected-files-item">
                                        <img class="selected-img" src="{{ (is_array($icon) && @is_file_exists($icon['image_80X80'])) ? static_asset($icon['image_80X80']) : asset('images/default/80X80.png') }}" alt="favicon">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="mb-4">
                                    <label for="admin_panel_copyright_text" class="form-label">{{ __('copyright_text') }}</label>
                                    <input type="text" class="form-control rounded-2" id="admin_panel_copyright_text"
                                           placeholder="{{__('copyright_text') }}" name="admin_panel_copyright_text" value="{{ old('admin_panel_copyright_text', setting('admin_panel_copyright_text', $lang) ) }}">
                                    <div class="nk-block-des text-danger">
                                        <p class="admin_panel_copyright_text_error error"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                            @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $(document).on('change', '#default_storage', function () {
                var storage = $(this).val();
                if (storage == 'aws_s3') {
                    $('.aws_div').removeClass('d-none');
                    $('.wasabi_div').addClass('d-none');
                } else if (storage == 'wasabi') {
                    $('.aws_div').addClass('d-none');
                    $('.wasabi_div').removeClass('d-none');
                } else {
                    $('.aws_div').addClass('d-none');
                    $('.wasabi_div').addClass('d-none');
                }
            });
        });
    </script>
@endpush
