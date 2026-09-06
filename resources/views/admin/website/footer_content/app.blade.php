@extends('backend.layouts.master')
@section('title', __('useful_link_settings'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.website.sidebar_component')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <h3 class="section-title">{{ __('useful_links') }}</h3>
                    <div class="default-tab-list default-tab-list-v2  bg-white redious-border website-setting-social-link p-20 p-sm-30">
                        @include('admin.website.component.footer_setting_sidebar')
                        <form action="{{ route('footer.update-setting') }}" method="POST" class="form">@csrf
                            <input type="hidden" name="site_lang" value="{{$lang}}">
                            <div class="row gx-20">
                                <div class="d-flex gap-12 sandbox_mode_div mb-4">
                                    <input type="hidden" name="show_apps_link" value="{{ setting('show_apps_link') == 1 ? 1 : 0 }}">
                                    <label class="form-label"
                                           for="show_apps_link">{{ __('show_apps_link') }}</label>
                                    <div class="setting-check">
                                        <input type="checkbox" value="1" id="show_apps_link" name="show_apps_link"
                                               class="sandbox_mode" {{ setting('show_apps_link') == 1 ? 'checked' : '' }}>
                                        <label for="show_apps_link"></label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <label for="play_store_link" class="form-label">{{__('play_store_link') }}</label>

                                            <span class="info-content">
                                                <i class="las la-info-circle" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="{{__('For remove the link keep this field blank')}}"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control rounded-2" id="play_store_link" name="play_store_link"
                                               placeholder="{{ __('enter_play_store_link') }}" value="{{ setting('play_store_link')}}">
                                        <div class="nk-block-des text-danger">
                                            <p class="play_store_link_error error"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="">

                                        <div class="d-flex align-items-center gap-2">
                                            <label for="app_store_link" class="form-label">{{__('app_store_link') }}</label>
                                            <span class="info-content">
                                                <i class="las la-info-circle" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="{{__('For remove the link keep this field blank')}}"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control rounded-2" id="app_store_link" name="app_store_link"
                                               placeholder="{{ __('enter_app_store_link') }}" value="{{ setting('app_store_link')}}">
                                        <div class="nk-block-des text-danger">
                                            <p class="app_store_link_error error"></p>
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
@push('js_asset')
    <script src="{{ static_asset('admin/js/jquery.nestable.min.js') }}"></script>
@endpush
@push('js')
    <script>
        $(document).ready(function () {
            $('#menuSortable').nestable({
            group: 'list',
            animation: 200,
            ghostClass: 'ghost',
            maxDepth: 1,
            });
        });
        $(document).on("click",'#add-menu-item',function() {
            var selector = $('#clone_menu .menu-item');
            var id = $('#dd-list .menu-item').last().data("id");
            var $copy  = selector.clone().appendTo('#dd-list');
            if (isNaN(id))
                id =0;

            $('#dd-list .menu-item').last().attr("data-id", ++id);

        });

    </script>
@endpush
