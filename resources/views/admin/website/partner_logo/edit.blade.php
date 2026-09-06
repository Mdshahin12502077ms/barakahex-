@extends('backend.layouts.master')
@section('title', __('partner_logo'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('edit_partner_logo') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <form action="{{ route('partner-logo.update', $partner_logo->id) }}" class="form-validate form"
                                    method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <input type="hidden" name="id" value="{{ $partner_logo->id }}">
                                    <input type="hidden"
                                       value="{{ @$partner_logo_language->translation_null == 'not-found' ? '' : @$partner_logo_language->id }}"
                                       name="translate_id">
                                    <input type="hidden" class="is_modal" value="0"/>
                                    <div class="col-lg-12 input_file_div">
                                        <div class="mb-3">
                                            <label class="form-label mb-1">{{__('logo') }}</label>
                                            <label for="image" class="file-upload-text">
                                                <p></p>
                                                <span class="file-btn">{{__('choose_file') }}</span>
                                            </label>
                                            <input class="d-none file_picker @error('partner_logo') is-invalid @enderror" type="file" id="image"
                                                   name="partner_logo">
                                            @if ($errors->has('partner_logo'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('partner_logo') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="selected-files d-flex flex-wrap gap-20">
                                            <div class="selected-files-item">
                                                <img class="selected-img" src="{{ getFileLink('80X80', $partner_logo->image) }}"
                                                     alt="favicon">
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
@push('css_asset')
    <link rel="stylesheet" href="{{ static_asset('admin/css/dropzone.min.css') }}">
@endpush
@push('js_asset')
    <!--====== media.js ======-->
    <script src="{{ static_asset('admin/js/dropzone.min.js') }}"></script>
@endpush
@push('js')
    <script src="{{ static_asset('admin/js/media.js') }}"></script>
@endpush
