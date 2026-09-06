@extends('backend.layouts.master')
@section('title', __('pricing_section_content'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('pricing_section_content') }}</h3>
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <form action="{{ route('admin.pricing.section') }}" method="POST" class="form" enctype="multipart/form-data">@csrf
                            <div class="row gx-20">
                                <input type="hidden" value="0" class="is_modal" name="is_modal">
                                <input type="hidden" name="site_lang" value="{{$lang}}">
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="price_title" class="form-label">{{ __('title') }}</label>
                                        <input type="text" class="form-control rounded-2" id="price_title"
                                                name="price_title" value="{{ setting('price_title', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="price_title_error error">{{ $errors->first('lang') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="price_subtitle" class="form-label">{{ __('subtitle') }}</label>
                                        <input type="text" class="form-control rounded-2" id="price_subtitle"
                                                name="price_subtitle" value="{{ setting('price_subtitle', $lang) }}">
                                        <div class="nk-block-des text-danger">
                                            <p class="price_subtitle_error error">{{ $errors->first('lang') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="editor-wrapper mb-4">
                                        <label for="description" class="form-label">{{__('description') }}</label>

                                        <textarea class="template-body price_description" name="price_description">{!! setting('price_description', app()->getLocale()) !!}</textarea>
                                        <div class="nk-block-des text-danger">
                                            <p class="price_description_error error">{{ $errors->first('price_description', $lang) }}</p>
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
@push('js')
    <script>
        $('.price_description').summernote({
            height: 210,
        });

    </script>
@endpush


