
@extends('backend.layouts.master')
@section('title', __('about'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('about') }}</h3>
                        <div class="oftions-content-right mb-12">
                            <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2" id="addImageButton">
                                @if(setting('about_image'))
                                    <span>{{ __('update_image') }}</span>
                                @else
                                    <i class="las la-plus"></i>
                                    <span>{{ __('add_image') }}</span>
                                @endif
                            </a>
                            <a href="{{ route('abouts.create') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                                <i class="las la-plus"></i>
                                <span>{{__('add_about') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                            <div class="default-list-table table-responsive yajra-dataTable">
                                {{ $dataTable->table() }}
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                                <form action="{{ route('about.image') }}" method="POST" enctype="multipart/form-data" id="addImageForm" style="display: none;">
                                    @csrf
                                    <div class="col-lg-12 input_file_div">
                                        <div class="mb-3">
                                            <label class="form-label mb-1">{{__('image') }}</label>
                                            <div class="p-0 border d-flex align-items-center" style="border-radius: 4px; height: 40px">
                                                <label for="unique_feature_image" class="file-upload-text text-ellips">
                                                    <p></p>
                                                    <span class="file-btn h-full ms-auto d-block " style="width: 120px">{{__('choose_file') }}</span>
                                                </label>
                                            </div>

                                            <input class="d-none file_picker" type="file" id="unique_feature_image"
                                                    name="about_image">
                                            <div class="nk-block-des text-danger">
                                                <p class="about_image_image_error error">{{ $errors->first('about_image') }}</p>
                                            </div>
                                        </div>
                                        <div class="selected-files d-flex flex-wrap gap-20">
                                            <div class="selected-files-item">
                                                <img class="selected-img" src="{{  getFileLink('80X80',setting('about_image')) }}"
                                                        alt="favicon">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center">
                                        <button type="submit" class="btn sg-btn-primary">{{__('submit') }}</button>
                                        @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
                                    </div>
                                </form>
                                <div class="col-lg-12 input_file_div" id="ImagePreview">
                                    <div class="selected-files d-flex align-items-center justify-content-center flex-wrap gap-20">
                                        <div class="selected-files-item">
                                            <img src="{{  getFileLink('original_image',setting('about_image')) }}"
                                                    alt="favicon">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('common.delete-script')
    @include('common.change-status-ajax')
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#addImageButton').on('click', function() {
                $('#addImageForm').toggle();
                $('#ImagePreview').toggle();
            });
        });
    </script>
@endpush


